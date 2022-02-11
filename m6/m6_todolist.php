<!DOCTYPE html>

<html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>mission_6-todolist</title>
        <link rel="stylesheet" href="m6_todolist.css">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    </head>

    <body>
        <h1 class="daimei">ToDoList</h1>
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
                <br><br><br>
                <p>チェックをつけると、</p>
                <p>項目の色が変わります。</p>
                <p>でも更新すると元通り真っ白です。</p>
                <p>ごめん</p>
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
                    if(!empty($_POST["postcomment"]) && !empty($_POST["postlimit_date"])){
        
                        //各変数
                        $postname = $_SESSION['name'];
                        $postcomment = $_POST["postcomment"];
                        $postlimit_date = $_POST["postlimit_date"];
        
                        //INSERT文：データを入力（データレコードの挿入）
                        $sql = $pdo -> prepare("INSERT INTO todolist (name, item, limit_date) VALUES (:name, :item, :limit_date)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':item', $item, PDO::PARAM_STR);
                        $sql -> bindParam(':limit_date', $limit_date, PDO::PARAM_STR);
        
                        //フォームからの変数をデータベースのやつに代入
                        $name = $postname;
                        $item = $postcomment;
                        $limit_date = $postlimit_date;
        
                        $sql -> execute();
                    }
                ?>

                <hr class="kugiri"/>
                
                <h3>□□□</h3>
                <br>

                <?php
                    //削除
                    if(isset($_POST["delete_submit"])){
                        if($_SESSION['name'] == $row['name']){
                            $id = $_POST["id"];

                            $sql = "DELETE FROM todolist WHERE id=:id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $stmt->execute();
                        }else{
                            echo "削除できません。";
                        }
                    }
                ?>
                   
                <?php
                    $sql = 'SELECT * FROM todolist';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        $row['id'].' ';
                        $row['name'].' ';
                        $row['item'].' ';
                        $row['limit_date'].'<br>';
                        "<hr>";
                    }
                ?>

                <table border="1" align="left">
                    <tbody>
                        <?php foreach($results as $row): ?>
                            <tr>
                                <td>
                                    <?php echo $row['name']; ?>
                                </td>
                                <td class="check">
                                <div>
                                <input type="checkbox" id="check_input">
                                <div id="check_content"><?php echo $row['item']; ?></div>
                                </div>
                                </td>

                                <td><?php echo $row['limit_date']; ?></td>

                                <td>
                                <form action="" method="post">
                                <button name="delete_submit" class="botan">
                                <i class="fas fa-trash-alt fa-fw"></i>
                                <input type="hidden" name="id" value="<?=$row['id'];?>">
                                </button>
                                </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="child3">
				<div class="hidden_box">
					<label for="label1">＋</label>
					<input type="checkbox" id="label1"/>
					<div class="hidden_show">
						<!--非表示ここから-->  
							<p class= "form">
								<div class="fo-mu">
									<form action="" method="post">
                                    <input type="hidden" class="form" name="postname" placeholder="Name" value = "<?php echo $_SESSION['name']??""; ?>">
									<i class="fas fa-comments fa-fw"></i>
                                    <br>
                                    <textarea class="fo-mu" style= "width:200px; height:5em; font-size:15px;" name="postcomment" placeholder="Comment" value = "<?php echo $editcomment??""; ?>"></textarea>
									<!--
                                    <input type="text" class="form" style= "width: 200px;height: 10em;" name="postcomment" placeholder="Comment" value = "<?php echo $editcomment??""; ?>">
                                    -->
                                    &emsp;
									<br>
									<input type="hidden" class="form" name="editnumber" value = "<?php echo $edit; ?>">
									<i class="fas fa-clock fa-fw"></i>
                                    <br>
									<input type="date" class="fo-mu" name="postlimit_date" placeholder="Limit Date" value="<?php echo $editlimit_date??""; ?>">
									&nbsp;&nbsp;
                                    <br>
									<button type="submit" class= "botan" name="submit">送信</button>
									</form>
								</div>
							</p>
						</p>
						<!--ここまで-->
					</div>
				</div>

            </div>
        </div>
    </body>
</html>