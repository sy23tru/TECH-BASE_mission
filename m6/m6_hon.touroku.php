<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_6-hon_touroku</title>
        <link rel="stylesheet" href="m6.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>

    <body>

        <h1 class="daimei">会員登録</h1>

        <?php
            session_start();

            //クロスサイトリクエストフォージェリ（CSRF）対策
            $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
            $token = $_SESSION['token'];
            //クリックジャッキング対策
            header('X-FRAME-OPTIONS: SAMEORIGIN');

            // DB接続設定
            $dsn = 'mysql:dbname=tb230373db;host=localhost';
            $user = 'tb-230373';
            $password = 'pnFrfrdmD9';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

            //GETデータを変数に入れる
            $urltoken = isset($_GET["urltoken"]) ? $_GET["urltoken"] : NULL;

            //メール入力判定
            if ($urltoken == ''){
                $errors['urltoken'] = "トークンがありません。";
            }else{               
                // DB接続	
                //flagが0の未登録者 or 仮登録日から24時間以内
                $sql = "SELECT mailadress FROM pre_user WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour";
                $stm = $pdo->prepare($sql);
                $stm->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
                $stm->execute();
                
                //レコード件数取得
                $row_count = $stm->rowCount();
                
                //24時間以内に仮登録され、本登録されていないトークンの場合
                if( $row_count ==1){
                    $mailadress_array = $stm->fetch();
                    $mailadress = $mailadress_array["mailadress"];
                    $_SESSION['mailadress'] = $mailadress;
                }else{
                    $errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎたかURLが間違えている可能性がございます。もう一度登録をやりなおして下さい。";
                }
            }
            
            

            //確認する(btn_confirm)押した後の処理
            if(isset($_POST['btn_confirm'])){
                //POSTされたデータを各変数に入れる
                $name = isset($_POST['name']) ? $_POST['name'] : NULL;
                $password = isset($_POST['password']) ? $_POST['password'] : NULL;
                
                //セッションに登録
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;

                //アカウント入力判定
                //パスワード入力判定
                if ($password == ''):
                    $errors['password'] = "パスワードが入力されていません。";
                else:
                    $password_hide = str_repeat('*', strlen($password));
                endif;

                if ($name == ''):
                    $errors['name'] = "氏名が入力されていません。";
                endif;       
            }
                
            

            //登録(btn_submit)押した後の処理
            if(isset($_POST['btn_submit'])){
                //パスワードのハッシュ化
                $password_hash =  password_hash($_SESSION['password'], PASSWORD_DEFAULT);

                //ここでデータベースに登録する
                try{
                    $sql = "INSERT INTO user (name,password,mailadress,status,created_at,updated_at) VALUES (:name,:password_hash,:mailadress,1,now(),now())";
                    $stm = $pdo->prepare($sql);
                    $stm->bindValue(':name', $_SESSION['name'], PDO::PARAM_STR);
                    $stm->bindValue(':mailadress', $_SESSION['mailadress'], PDO::PARAM_STR);
                    $stm->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                    $stm->execute();

                    //pre_userのflagを1にする(トークンの無効化)
                    $sql = "UPDATE pre_user SET flag=1 WHERE mailadress=:mailadress";
                    $stm = $pdo->prepare($sql);
                    //プレースホルダへ実際の値を設定する
                    $stm->bindValue(':mailadress', $mailadress, PDO::PARAM_STR);
                    $stm->execute();
                                    
                    //メール送信処理    
                    require 'phpmailer/src/Exception.php';
                    require 'phpmailer/src/PHPMailer.php';
                    require 'phpmailer/src/SMTP.php';
                    require 'phpmailer/setting2.php';

                    // PHPMailerのインスタンス生成
                    $mail = new PHPMailer\PHPMailer\PHPMailer();

                    $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
                    $mail->SMTPAuth = true;
                    $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
                    $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
                    $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
                    $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
                    $mail->Port = SMTP_PORT; // 接続するTCPポート

                    // メール内容設定
                    $mail->CharSet = "UTF-8";
                    $mail->Encoding = "base64";
                    $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);

                    $sql = "SELECT mailadress FROM user WHERE mailadress = (:mailadress)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':mailadress', $mailadress, PDO::PARAM_STR);
                    $stmt->execute();
                    $member = $stmt->fetch();

                    $mail->addAddress($member['mailadress']); //受信者（送信先）を追加する
                    //    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
                    //    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
                    //    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
                    $mail->Subject = MAIL_SUBJECT; // メールタイトル
                    $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
                    $url="https://tech-base.net/tb-230373/m6/m6_login.php";
                    $body =
    	                '本登録が完了しました。<br>
                        下記のURLよりログインをお願いします。' . "<br>" . $url;

                    $mail->Body  = $body; // メール本文
                    // メール送信の実行
                    if(!$mail->send()) {
    	                echo 'メッセージは送られませんでした！';
    	                echo 'Mailer Error: ' . $mail->ErrorInfo;
                    } else {
    	                echo "本登録が完了しました。";
                    }

                    //データベース接続切断
                    $stm = null;

                    //セッション変数を全て解除
                    $_SESSION = array();
                    //セッションクッキーの削除
                    if (isset($_COOKIE["PHPSESSID"])) {
                            setcookie("PHPSESSID", '', time() - 1800, '/');
                    }
                    //セッションを破棄する
                    session_destroy();

                }catch (PDOException $e){
                    //トランザクション取り消し（ロールバック）
                    $pdo->rollBack();
                    $errors['error'] = "もう一度やりなおして下さい。";
                    print('Error:'.$e->getMessage());
                }
            }

        ?>



        <!-- page_3 完了画面-->
        <?php if(isset($_POST['btn_submit'])): ?>
        <br>
        <p>ログインは<a href="https://tech-base.net/tb-230373/m6/m6_login.php" target="_blank">こちら</a>から</p>

        <!-- page_2 確認画面-->
        <?php elseif (isset($_POST['btn_confirm'])): ?>
            <form action="" method="post">
                <p>メールアドレス：<?=$mailadress?></p>
                <p>パスワード：<?=$password_hide?></p>
                <p>氏名：<?=$name?></p>
                <br>
                <p>上記の内容で登録してよろしいですか？</p>
                
                <input type="submit" class="botan" name="btn_back" value="戻る">
                <input type="hidden" name="token" value="<?=$_POST['token']?>">
                <input type="submit" class="botan" name="btn_submit" value="登録する">
            </form>
        <?php else: ?>

        <!-- page_1 登録画面 -->
            <?php if(!isset($errors['urltoken_timeover'])): ?>
                <form action="" method="post">
                    <p>メールアドレス：　<input type="text" style= "width:300px; height:1.5em; font-size:15px;" name="mailadress" value="<?php echo $mailadress??""; ?>" placeholder="メールアドレス"></p>
                    <p>パスワード：　　　<input type="password" style= "width:300px; height:1.5em; font-size:15px;" name="password" placeholder="パスワード"></p>
                    <p>氏名：　　　　　　<input type="text" style= "width:300px; height:1.5em; font-size:15px;" name="name" placeholder="氏名"></p>
                    <input type="hidden" name="token" value="<?=$token?>">
                    <input type="submit" class="botan" name="btn_confirm" value="確認">
                </form>
            <?php endif ?>
        <?php endif; ?>

    </body>
</html>