<?php
    require("m6_todolist.php");

    $dsn = 'mysql:dbname=tb230373db;host=localhost';
    $user = 'tb-230373';
    $password = 'pnFrfrdmD9';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //編集
    //編集選択
    if(isset($_POST["edit_submit"])){
        $id = $_POST["id"];

        /*
        $sql = "SELECT * FROM todolist WHERE id=:id";
        $edit_select = $pdo->prepare($sql);
        $edit_select->execute();
        $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
        $editcomment = $edit_select[0]['comment'];
        */

        
        $sql = 'SELECT * FROM todolist WHERE id=:id';//レコードを取得
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row) {
            $editcomment = $row['item'];
            $editlimit_date = $row['limit_date'];
        }
        

        /*
        $edit_select = $pdo->prepare("SELECT * FROM todolist WHERE id= :id");
        $edit_select->execute();
        $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
        $editcomment =  $edit_select[0]['item'];

        $edit_select = $pdo->prepare("SELECT * FROM todolist WHERE id= :id");
        $edit_select->execute();
        $edit_select = $edit_select->fetchAll(PDO::FETCH_ASSOC);
        $editlimit_date =  $edit_select[0]['limit_date'];
        */
    }


    //編集実行
    if(!empty($_POST["editnumber"])){

        //各変数
        $editnumber = $_POST["editnumber"];
        $postcomment = $_POST["postcomment"];
        $postlimit_date = $_POST["postlimit_date"];

        $id = $editnumber; //変更する投稿番号
        $item = $postcomment;//変更内容
        $limit_date = $postlimit_date;
        $sql = 'UPDATE todolist SET item=:item, limit_date=:limit_date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':item', $item, PDO::PARAM_STR);
        $stmt->bindParam(':limit_date', $limit_date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
?>