 <?php
    //データベースの準備
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $DatabaseName = "UserNameAndPassWord";
    $sql = "CREATE TABLE IF NOT EXISTS ".$DatabaseName
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    
    
    //POSTから受け取って変数に格納
    
    //新規登録フォーム
    if(!empty($_POST["NewUserName"])){
        $NewUserName = $_POST["NewUserName"];
    } 
    if(!empty($_POST["NewPassword"])){
        $NewPassword = $_POST["NewPassword"];
    }
    
    $add=false;
    if(!empty($NewUserName) && !empty($NewPassword)){
        $sql = $pdo -> prepare("INSERT INTO ".$DatabaseName." (name, password) VALUES (:name, :password)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
        
        $name = $NewUserName;
        $password = $NewPassword;
        
        $sql -> execute();
        $add=true;
    }
    
    //ログインフォーム
    if(!empty($_POST["UserName"])){
        $UserName = $_POST["UserName"];
    } 
    if(!empty($_POST["Password"])){
        $Password = $_POST["Password"];
    }
    
    $success=false;
    if(!empty($UserName) && !empty($Password)){
        $sql = 'SELECT * FROM '.$DatabaseName.' WHERE name="'.$UserName.'"';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll();
        $CorrectPass=$results[0]['password'];
        if($CorrectPass==$Password){
            header("Location: main.php");
            $success=true;
        }
    }
    //パスワード抽出の関数
    function pass($DatabaseName1,$pdo1,$UserName1,$Password1){
        $sql = 'SELECT * FROM '.$DatabaseName1.' WHERE name="'.$UserName1.'"';
        $stmt = $pdo1->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll();
        $CorrectPass=$results[0]['password'];
        if($CorrectPass==$Password1){
            header("main.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログイン画面</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <header>
            <div class="title">
                <h1>とらねこけいじばん≦^.≡.^≧</h1>
            </div>
        </header>
        <div class="forms">
            <div class='addform'>
                <form method="POST">
                    <a>新規登録</a>
                    <input class="nameandpass" type="text" name="NewUserName" Placeholder='ユーザー名'><br>
                    <input class='nameandpass' type='text' name='NewPassword' Placeholder='パスワード'><br>
                    <button class='button' type='submit'>登録</button>
                </form>
                <?php
                    if(!empty($NewUserName) && !empty($NewPassword) && $add==true){
                        echo "<h3>ユーザー名：".$NewUserName."、パスワード：".$NewPassword." を登録しました</h3>";
                    }
                ?>
            </div>
            <div class='loginform'>
                <form method="POST">
                    <a>ログイン</a>
                    <input class="nameandpass" type="text" name="UserName" Placeholder='ユーザー名'><br>
                    <input class='nameandpass' type='text' name='Password' Placeholder='パスワード'><br>
                    <button class='button' type='submit'>ログイン</button>
                </form>
                <?php
                    if(!empty($UserName) && !empty($Password) && $success==false){
                        echo "<h3>ユーザー名またはパスワードが間違っています</h3>";
                    }
                ?>
            </div>
        </div>
    </body>
</html>
