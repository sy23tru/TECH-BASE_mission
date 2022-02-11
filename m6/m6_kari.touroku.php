<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_6-kari_touroku</title>
        <link rel="stylesheet" href="m6.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>

    <body>

        <h1 class="daimei">仮会員登録</h1>

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



            //送信ボタンが押された場合
            if(isset($_POST['mailadress'])) {
                //メールアドレスが空欄の場合
                if(empty($_POST["mailadress"])){
                    echo "メールアドレスが未入力です。";
                }else{
                    //POSTされたデータを変数に入れる
                    $mailadress = $_POST["mailadress"];
   
                    //メールアドレス構文チェック
                    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mailadress)){
			        echo "メールアドレスの形式が正しくありません。";
                    }

                    //DB確認 
                    //メールアドレスに入力されたメアドと同じものいくつあるか数える       
                    $stmt = $pdo->prepare("SELECT count(*) FROM user WHERE mailadress='$mailadress'");
                    $stmt->execute();
                    $count = $stmt->fetchColumn();

                    //0個の場合
                    if($count <= 0) {
                        //URLを取得
                        $urltoken = hash('sha256',uniqid(rand(),1));
                        $url = "https://tech-base.net/tb-230373/m6/m6_hon.touroku.php"."?urltoken=".$urltoken;

                        //データベースのpre_userテーブルに登録する
                        $sql = "INSERT INTO pre_user (urltoken, mailadress, date, flag) VALUES (:urltoken, :mailadress, now(), '0')";
                        $stm = $pdo->prepare($sql);
                        $stm->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
                        $stm->bindValue(':mailadress', $mailadress, PDO::PARAM_STR);
                        $stm->execute();
                        $pdo = null;

                        //メール送信処理    
                        require 'phpmailer/src/Exception.php';
                        require 'phpmailer/src/PHPMailer.php';
                        require 'phpmailer/src/SMTP.php';
                        require 'phpmailer/setting.php';

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
                        $mailadress = $_POST["mailadress"];
                        $to = $mailadress;
                        $mail->addAddress($to); //受信者（送信先）を追加する
                        //    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
                        //    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
                        //    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
                        $mail->Subject = MAIL_SUBJECT; // メールタイトル
                        $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
                        $body = 
                        'この度はご登録いただきありがとうございます。<br>
                        24時間以内に下記のURLからご登録下さい。' . "<br>" . $url;
                        
                        $mail->Body  = $body; // メール本文
                        // メール送信の実行
                        if(!$mail->send()) {
                            echo 'メッセージは送られませんでした！';
                            echo 'Mailer Error: ' . $mail->ErrorInfo;
                        } 
                        /*else {
                            echo "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
                        }
                        */

                    }else{
                        //user テーブルに同じメールアドレスがある場合、エラー表示
                        echo "このメールアドレスはすでに利用されています。";
                    }                    
                }
            }
                
        ?>

        <?php if (isset($_POST['submit'])): ?>
            <!-- 登録完了画面 -->
            <br>
            <p>メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。</p>
        <?php else: ?>
            <form action="" method="post">
                <p>メールアドレス：　<input type="text" style= "width:300px; height:1.5em; font-size:15px;" name="mailadress" placeholder="メールアドレス"></p> 
                <input type="hidden" name="token" value="<?=$token?>">
                <input type="submit" class="botan" name="submit" value="送信">
            </form>

            <br>
            <p>登録済みの方は<a class="rink" href="https://tech-base.net/tb-230373/m6/m6_login.php" target="_blank">こちら</a>から</p>
        <?php endif; ?>
    </body>
</html>