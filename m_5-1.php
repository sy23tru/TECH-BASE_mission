<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
    </head>

    <body>

        <h1>掲示板</h1>
        <br>

        <?php
                
            //DB接続設定
            $dsn = 'データベース名';
            $user = 'ユーザー名';
            $password = 'パスワード';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));            



            //投稿機能
            if(!empty($_POST["postname"]) && !empty($_POST["postcomment"]) && !empty($_POST["postpassword"])){

                //各変数
                $postname = $_POST["postname"];
                $postcomment = $_POST["postcomment"];
                $date = date("Y/m/d H:i:s"); 
                $postpassword = $_POST["postpassword"];

                //INSERT文：データを入力（データレコードの挿入）
                $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, created_at, password) VALUES (:name, :comment, :created_at, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':created_at', $created_at, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);

                //フォームからの変数をデータベースの変数に代入
                $name = $postname;
                $comment = $postcomment;
                $created_at = $date;
                $password = $postpassword;

                $sql -> execute();

            }



            //削除機能
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



            //編集選択機能
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



        
        
        <br><br><br>
        【　投稿フォーム　】
        <form action="" method="post">
            名前:　　　　<input type="text" name="postname" placeholder="名前" value = "<?php echo $editname??""; ?>"> <br>
            コメント:　　<input type="text" name="postcomment" placeholder="コメント" value = "<?php echo $editcomment??""; ?>"> <br>
            <input type="hidden" name="editnumber" value = "<?php echo $edit; ?>">
            パスワード:　<input type="password" name="postpassword" placeholder="パスワード">
            <input type="submit" name="submit">
        </form> <br>

    
        【　削除フォーム　】
        <form action="" method="post">
            投稿番号:　　<input type="number" name="delete" placeholder="削除対象番号"> <br>
            パスワード:　<input type="password" name="delpass" placeholder="パスワード">
            <button type="submit" name="submit">削除</button>
        </form> <br>



        【　編集フォーム　】
        <form action="" method="post">
            投稿番号:　　<input type="number" name="edit" placeholder="編集対象番号"> <br>
            パスワード:　<input type="password" name="edipass" placeholder="パスワード">
            <button type="submit" name="submit">編集</button>
        </form> <br>

        ーーーーーーーーーーーーーーーーーーーーーーーー 
        <br>
        【　投稿一覧　】 
        <br><br><br>



        <?php

            //テーブルに登録されたデータを取得・表示
            $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo $row['id'].' ';
                echo $row['name'].' ';
                echo $row['comment'].' ';
                echo $row['created_at'].' ';
                echo $row['password'].'<br>';
                echo "<hr>";
            }

        ?>
    
    </body>
</html>