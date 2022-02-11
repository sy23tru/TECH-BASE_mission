<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission6_logout</title>
        <link rel="stylesheet" href="m6.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    </head>

    <body>

        <h1 class="daimei">ログアウト</h1>

            </form>

            <?php

                session_start();
                $_SESSION = array();//セッションの中身をすべて削除
                session_destroy();//セッションを破壊

            ?>

                <p>ログアウトしました。</p>
                <a href="m6_login.php">ログイン</a>

    </body>
</html>