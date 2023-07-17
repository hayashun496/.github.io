
 <!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>掲示板</title>
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <header>
            <div class="title">
                <h1>とらねこけいじばん≦^.≡.^≧</h1>
            </div>
        </header>
        <div class="content">
            <?php
                //データベースの準備
                $dsn = 'mysql:dbname=ユーザー名;host=localhost';
                $user = 'ユーザー名';
                $password = 'パスワード';
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                
                $DatabaseName = "m6";
                $sql = "CREATE TABLE IF NOT EXISTS ".$DatabaseName
                ." ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name char(32),"
                . "comment TEXT,"
                . "date TEXT,"
                . "image TEXT,"
                . "password TEXT"
                .");";
                $stmt = $pdo->query($sql);
                
                
                
                //POSTから受け取って変数に格納
                
                //新規入力フォーム
                if(!empty($_POST["NewName"])){
                    $NewName = $_POST["NewName"];
                } 
                if(!empty($_POST["NewComment"])){
                    $NewComment = $_POST["NewComment"];
                } 
                if(!empty($_FILES["Image"])){
                    //画像ファイルのパスを決定・保存
$Image = "images/".basename($_FILES["Image"]["name"]);
move_uploaded_file($_FILES["Image"]["tmp_name"], $Image);
                }
                
                //編集フォーム
                if(!empty($_POST["EditNumber"])){
                    $EditNumber = $_POST["EditNumber"];
                } 
                if(!empty($_POST["EditName"])){
                    $EditName = $_POST["EditName"];
                } 
                if(!empty($_POST["EditComment"])){
                    $EditComment = $_POST["EditComment"];
                }
                if(!empty($_POST["EditFinishNumber"])){
                    $EditFinishNumber = $_POST["EditFinishNumber"];
                } 
                
                //削除フォーム
                if(!empty($_POST["DeleteNumber"])){
                    $DeleteNumber = $_POST["DeleteNumber"];
                }
                
                //パスワードフォーム
                if(!empty($_POST["Password"])){
                    $Password = $_POST["Password"];
                }
                
                
                
                //新しいコメント
                if(!empty($NewComment)){
                    if(!empty($Password)){
                        $sql = $pdo -> prepare("INSERT INTO ".$DatabaseName." (name, comment, date, image, password) VALUES (:name, :comment, :date, :image, :password)");
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':image', $image, PDO::PARAM_STR);
                        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                        
                        $name = $NewName;
                        $comment = $NewComment;
                        $date = date("Y/m/d H:i:s");
                        $image = $Image;
                        $password = $Password;
                        
                        $sql -> execute();
                    }else{
                        echo "<h3>パスワードを入力してください</h3>";
                    }
                }
                
                
                //パスワード抽出の関数
                function pass($DatabaseName1,$pdo1,$Num){
                    $id = $Num; // idがこの値のデータだけを抽出したい
                    $sql = 'SELECT * FROM '.$DatabaseName1.' WHERE id=:id';
                    $stmt = $pdo1->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                    $stmt->execute();                             // ←SQLを実行する。
                    
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        $CorrectPass = $row['password'];
                    }
                    return $CorrectPass; //パスワードを抽出して返す
                }
                
                
                //コメント編集フォームに既存のコメントを表示するためのコード
                if(!empty($EditNumber)){
                    if(pass($DatabaseName,$pdo,$EditNumber)==$Password){
                        $id = $EditNumber; // idがこの値のデータだけを抽出したい
                        $sql = 'SELECT * FROM '.$DatabaseName.' WHERE id=:id';
                        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                        $stmt->execute();                             // ←SQLを実行する。
                        $results = $stmt->fetchAll();
                        foreach ($results as $row){
                            $EditComment = $row['comment'];
                            $EditName = $row['name'];
                        }
                    }else{
                        echo "<h3>パスワードが異なります</h3>";
                    }
                }
                
                //コメントを編集
                if(!empty($EditComment) && !empty($EditFinishNumber)){
                    $id = $EditFinishNumber;
                    $name = $EditName;
                    $comment = $EditComment;
                    $date = date("Y/m/d H:i:s");
                    
                    $sql = 'UPDATE '.$DatabaseName.' SET name=:name,comment=:comment,date=:date WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                
                
                //コメントを削除
                if(!empty($DeleteNumber)){
                    if(pass($DatabaseName,$pdo,$DeleteNumber)==$Password){
                        //画像ファイルの名前を取得
                        $id = $DeleteNumber ; // idがこの値のデータだけを抽出したい
                        $sql = 'SELECT * FROM '.$DatabaseName.' WHERE id=:id ';
                        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                        $stmt->execute();                             // ←SQLを実行する。
                        $results = $stmt->fetchAll();
                        unlink($results[0]['image']);
                        
                        
                        $id = $DeleteNumber;
                        $sql = 'delete from '.$DatabaseName.' where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                    }else{
                        echo "<h3>パスワードが異なります</h3>";
                    }
                }
                
                
            ?>
            <div class='table'>
                <div class='table-title'>
                    <!--<a class='id'>番号</a>-->
                    <a class='name name-title'>名前</a>
                    <div class='commentarea'>
                    <a class='comment'>コメント</a>
                    </div>
                    <a class='image'>猫様のご尊顔</a>
                    <a class='pass'>パスワード</a>
                    <div class='clear'></div>
                </div>
                    
                <div class='lines'>
                <?php
                    //コメントを表示
                    
                    $sql = 'SELECT * FROM '.$DatabaseName;
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
                    $lastid=0;
                    foreach ($results as $row){
                        echo "<div class='line'>";
                            //番号
                            echo "<a class='id'>".$row['id']."</a>";
                            
                            //編集時のみ編集フォーム
                            if(!empty($EditNumber) && $row['id']==$EditNumber && pass($DatabaseName,$pdo,$EditNumber)==$Password){
                                echo "<form class='editform' method='post'>";
                                    echo "<input class='editname' type='textarea' name='EditName' value=".$EditName.">";
                                    echo "<textarea class='editcomment' type='textarea' rows='3' name='EditComment'>".$EditComment."</textarea>";
                                    echo "<button class='button' type='submit' name='EditFinishNumber' value=".$row['id'].">完了</button>";
                                echo "</form>";
                            }else{
                                echo "<a class='name'>".$row['name']."</a>";
                                echo "<div class='commentarea'>";
                                    echo "<a class='comment'>".$row['comment']."</a>";
                                echo "</div>";
                            }
                            
                            //日付
                            //echo "<a class='date'>".$row['date']."</a>";
                            echo "<div class='image'>";
                                echo "<img src=".$row['image'].">";
                            echo "</div>";
                            
                            
                            //削除編集ボタン
                            echo "<form class='deleteeditform' method='post'>";
                                echo "<input class='pass' type='text' name='Password' Placeholder='パスワード'>";
                                echo "<button class='button' type='submit' name='DeleteNumber' value=".$row['id'].">削除</button>";
                                echo "<button class='button' type='submit' name='EditNumber' value=".$row['id'].">編集</button>";
                            echo "</form>";
                            
                            echo "<div class='clear'></div>";
                        echo "</div>";
                        $lastid=$row['id'];
                    }
                    
                    
                    //0行ならテーブル削除（idリセットのため）
                    $sql = 'SELECT * FROM '.$DatabaseName;
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    if(count($results)==0){
                        $sql = 'DROP TABLE '.$DatabaseName;
                        $stmt = $pdo->query($sql);
                    }
                ?>
                </div>
                
                
                <div class='add'>
                    <form class='newform' method='post' enctype="multipart/form-data">
                        <a class='id'><?php echo $lastid+1 ?></a>
                        <input class='editname' type='textarea' name="NewName" Placeholder="にゃまえ">
                        <textarea class='editcomment' type='textarea' rows='3' name='NewComment' Placeholder="ぬっこぬこにしてあげる(=^・^=)"></textarea>
                        <input class="newimage" type="file" name="Image">
                        <input class='pass newpass' type='text' name='Password' Placeholder='パスワード'>
                        <button class='button' type='submit'>追加</button>
                        <div class='clear'></div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
