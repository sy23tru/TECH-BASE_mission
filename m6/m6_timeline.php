<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_6-timeline</title>
        <link rel="stylesheet" href="m6_timeline.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    </head>

    <body>
        <h1 class="daimei">Timeline</h1>

        <p>&emsp13;</p>

        <div class="parent">
            <div class="child1">
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
                <p>こんにちは！</p>
                <p>
                    <?php 
                        session_start();

                        if(!$_SESSION['id'] > 0){
                            header("Location: https://tech-base.net/tb-230373/m6/m6_login.php");
                        } 

                        echo $_SESSION['name']; 
                    ?>
                    さん
                </p>
            </div>
            <div class="child2">

                <?php
                    // DB接続設定
                    $dsn = 'mysql:dbname=tb230373db;host=localhost';
                    $user = 'tb-230373';
                    $password = 'pnFrfrdmD9';
                    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                        
        
                    //コメント・パスワードが空欄じゃない(入力されている)場合
                    //投稿機能

                    if(isset($_POST["post_sub"])){
                        if(!empty($_POST["postcomment"]) && !empty($_POST["postpassword"])){
            
                            //各変数
                            $postname = $_SESSION['name'];
                            $postcomment = $_POST["postcomment"];
                            $date = date("Y/m/d H:i:s"); 
                            $postpassword = $_POST["postpassword"];
            
                            //INSERT文：データを入力（データレコードの挿入）
                            $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, created_at, password) VALUES (:name, :comment, :created_at, :password)");
                            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                            $sql -> bindParam(':created_at', $created_at, PDO::PARAM_STR);
                            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            
                            //フォームからの変数をデータベースのやつに代入
                            $name = $postname;
                            $comment = $postcomment;
                            $created_at = $date;
                            $password = $postpassword;
            
                            $sql -> execute();
                            //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
                        }
                    }
        
                    //削除対象番号とパスワードが両方とも空欄じゃない(入力されている)場合
                    //削除機能  
                    if(isset($_POST["delete_sub"])){ 
                        if(!empty($_POST["delete"]) && !empty($_POST["delpass"])){
            
                            //各変数
                            $delete = $_POST["delete"];
                            $delpass = $_POST["delpass"];
            
                            $id = $delete;
                            $sql = 'delete from tbtest where id=:id';
            
                            $delete_select = $pdo->prepare("SELECT * FROM tbtest WHERE id= '$delete'");
                            $delete_select->execute();
                            $delete_select = $delete_select->fetchAll(PDO::FETCH_ASSOC);
                            $password =  $delete_select[0]['password'];
            
                            if($delpass == $password){   //パスワードが一致した場合
            
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->execute();
            
                            } else {
                                echo "<span style='color:red;'>パスワードが違います</span>";
                            }
                        
                        }
                    }
        
        
                    //編集対象番号とパスワードが両方とも空欄じゃない(入力されている)場合
                    //編集選択機能
                    if(isset($_POST["edit_sub"])){
                        if(!empty($_POST["edit"]) && !empty($_POST["edipass"])){
            
                            //各変数
                            $edit = $_POST["edit"];
                            $edipass = $_POST["edipass"];
            
                            $id = $edit;
                            $sql = 'delete from tbtest where id=:id';
            
                            $edit_select = $pdo->prepare("SELECT * FROM tbtest WHERE id= '$edit'");
                            $edit_select->execute();
                            $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
                            $password =  $edit_select[0]['password'];
                        
                            if($edipass == $password){   //パスワードが一致した場合
            
                                $editnumber = $edit;
            
                                $edit_select = $pdo->prepare("SELECT * FROM tbtest WHERE id= '$edit'");
                                $edit_select->execute();
                                $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
                                $editname =  $edit_select[0]['name'];
            
                                $edit_select = $pdo->prepare("SELECT * FROM tbtest WHERE id= '$edit'");
                                $edit_select->execute();
                                $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
                                $editcomment =  $edit_select[0]['comment'];
            
                            } else {
                                echo "<span style='color:red;'>パスワードが違います</span>";
                            }
            
                        }
                    }
        
        
                    //編集対象番号をコピーしたフォ―ム(名前とコメントの下)から送信があった場合
                    //編集実行機能
                    if(!empty($_POST["editnumber"])){
        
                        //各変数
                        $editnumber = $_POST["editnumber"];
                        $postname = $_POST["postname"];
                        $postcomment = $_POST["postcomment"];
        
                        $id = $editnumber; //変更する投稿番号
                        $name = $postname;//変更内容
                        $comment = $postcomment;//変更内容
                        $sql = 'UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                ?>

                <hr class="kugiri"/>
                
                <h3>□□□</h3>
                <br>
        
                <?php
                    $sql = 'SELECT * FROM tbtest';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        echo $row['id'].' ';
                        echo $row['name'].' ';
                        echo $row['comment'].' ';
                        echo $row['created_at'].'<br>';
                        echo "<hr>";
                    }
                ?>

            </div>
            <div class="child3">   
                <p class= "form">
                    <h2>POST</h2> 
                    <div class="fo-mu">
                        <form action="" method="post">
                        <!-- <i class="fas fa-user fa-fw"></i> -->
                        <input type="hidden" class="form" name="postname" placeholder="Name" value = "<?php echo $_SESSION['name']??""; ?>">
                        <!-- <input type="text" class="form" name="postname" placeholder="Name" value = "<?php echo $editname??""; ?>"> -->
                        <i class="fas fa-comments fa-fw"></i>
                        <input type="text" class="form" name="postcomment" placeholder="Comment" value = "<?php echo $editcomment??""; ?>">
                        &emsp;
                        <br>
                        <input type="hidden" class="form" name="editnumber" value = "<?php echo $edit; ?>">
                        <i class="fas fa-key fa-fw"></i>
                        <input type="password" name="postpassword" placeholder="Password">
                        &nbsp;&nbsp;
                        <button type="submit" class= "botan" name="post_sub">投稿</button>
                        </form>
                    </div>
                </p>
                
                <p class="form">
                    <h2>DELETE</h2>
                    <div class="fo-mu">
                        <form action="" method="post">
                            <i class="fas fa-trash-alt fa-fw"></i>
                            <input type="number" class="form" name="delete" placeholder="Delete Number">
                            &emsp;
                            <br>
                            <i class="fas fa-key fa-fw"></i>
                            <input type="password" class="form" name="delpass" placeholder="Password">
                            &nbsp;&nbsp;
                            <button type="submit" class= "botan" name="delete_sub">削除</button>
                        </form>
                    </div>
                </p>

                <p class="form">
                    <h2>EDIT</h2>
                    <div class="fo-mu">
                        <form action="" method="post">
                            <i class="fas fa-edit fa-fw"></i>
                            <input type="number" class="form" name="edit" placeholder="Edit Number">
                            &emsp;
                            <br>
                            <i class="fas fa-key fa-fw"></i>
                            <input type="password" class="form" name="edipass" placeholder="Password">
                            &nbsp;&nbsp;
                            <button type="submit" class= "botan" name="edit_sub">編集</button>
                        </form>
                    </div>
                </p>

                <hr>

            </div>
        </div>
    </body>
</html>