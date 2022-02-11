<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission6_login</title>
        <link rel="stylesheet" href="m6_login.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>

    <body>

        <h1 class="daimei">ログイン</h1>

            <form action="" method="post">
            <p>メールアドレス：　<input type="text" style= "width:300px; height:1.5em; font-size:15px;" name="mailadress" placeholder="メールアドレス" required></p>
            <p>パスワード：　　　<input type="password" style= "width:300px; height:1.5em; font-size:15px;" name="password" placeholder="パスワード" required></p>
            <input type="submit" class="botan" name="btn_login" value="ログイン">
            </form>

            <br>
            <p>新規登録は<a href="https://tech-base.net/tb-230373/m6/m6_kari.touroku.php" target="_blank">こちら</a>から</p>

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

                //ログインボタン押した後の処理
                if(isset($_POST['btn_login'])){
                    //POSTされたデータを各変数に入れる
                    $mailadress = isset($_POST['mailadress']) ? $_POST['mailadress'] : NULL;
                    $password = isset($_POST['password']) ? $_POST['password'] : NULL;

                    $sql = "SELECT password,name,id FROM user WHERE mailadress = (:mailadress)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':mailadress', $mailadress, PDO::PARAM_STR);
                    $stmt->execute();
                    $member = $stmt->fetch();
                    //指定したハッシュがパスワードにマッチしているかチェック
                    if (password_verify($_POST['password'], $member['password'])) {

                        session_regenerate_id(true); //session_idを新しく生成し、置き換える
                        $_SESSION['mailadress'] = $member['mailadress'];
                        $_SESSION['name'] = $member['name'];
                        $_SESSION['id'] = $member['id'];

                        echo $_SESSION['name'] . "さんが";
                        echo "<br>";
                        echo "ログインしました。";
                        //リダイレクト
                        header("Location: https://tech-base.net/tb-230373/m6/m6_home.php");
                        exit;

                    }else{
                        echo "メールアドレスもしくはパスワードが間違っています。";
                        //echo '<a href="https://tech-base.net/tb-230373/m6/m6_pass.saitouroku.php">パスワードを忘れた場合はこちら</a>';
                    }   
                      
                }
                //エラー情報
                //print_r($pdo -> errorInfo());
            ?> 
    </body>
</html>