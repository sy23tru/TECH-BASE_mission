<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_6-home</title>
        <link rel="stylesheet" href="m6_home.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    </head>

    <body>
        <!--
        <div class="box">
            <img src="home1.jpg" alt="" class="img_01">
            <img src="home2.jpg" alt="" class="img_02">
        </div>
        -->

        <span class="appear">
            <h1 class="daimei">Home</h1>          
        </span>

        <span class="appear d1">
            <header>
                <div class="h-menu">
                    <input id="h-menu_checkbox" class="h-menuCheckbox" type="checkbox">
                    <label class="h-menu_icon" for="h-menu_checkbox"><span class="hamburger-icon"></span></label>
                    <label id="h-menu_black" class="h-menuCheckbox" for="h-menu_checkbox"></label>
                    <div id="h-menu_content">
                        <ul>
                        <li><a href="m6_home.php">ホーム</a></li>
                        <li><a href="m6_timeline.php">タイムライン</a></li>
                        <li><a href="m6_todolist.php">ToDoリスト</a></li>
                        <li><a href="#">□</a></li>
                        <li><a href="m6_logout.php">ログアウト</a></li>
                    </div>
                </div>
            </header>

        </span>
        
        <span class="appear d2">
            <h2 class="h2">
            <?php
                    session_start();

                    if(!$_SESSION['id'] > 0){
                        header("Location: https://tech-base.net/tb-230373/m6/m6_login.php");
                    }

                    echo "ようこそ、".$_SESSION['name']."さん";
                ?>
            </span>
            </h2>
    </body>
</html>