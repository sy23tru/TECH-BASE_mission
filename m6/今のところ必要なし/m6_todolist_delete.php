<?php
    require("m6_todolist.php");

    $dsn = 'mysql:dbname=tb230373db;host=localhost';
    $user = 'tb-230373';
    $password = 'pnFrfrdmD9';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //削除
    if(isset($_POST["delete_submit"])){
        $id = $_POST["id"];

        $sql = "DELETE FROM todolist WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        //echo "削除しました";//
        header("Location: https://tech-base.net/tb-230373/m6/m6_todolist.php");
        exit;
    }
?>