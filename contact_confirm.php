<?php
require_once("lib/util.php");
$gobackURL = "contact_input.html";

//文字エンコードの検証
if (!cken($_POST)){
    header("Location:{$gobackURL}");
    exit();
}
//簡単なエラー処理
$errors = [];

if(!isset($_POST["name"])||($_POST["name"]==="")){
    $errors[] = "名前が空です。";
}
if(!isset($_POST["furigana"])||($_POST["furigana"]==="")){
    $errors[] = "ふりがなが空です。";
}
if(!isset($_POST["tel"])||($_POST["tel"]==="")){
    $errors[] = "電話番号が空です。";
}
if(!isset($_POST["email"])||($_POST["email"]==="")){
    $errors[] = "メールアドレスが空です。";
}
if(!isset($_POST["message"])||($_POST["message"]==="")){
    $errors[] = "お問い合わせ内容が入力されていません。";
}

//エラーがあったとき
if(count($errors)>0){
    echo '<ol class="error">';
    foreach ($errors as $value){
        echo "<li>", $value, "</li>";
    }
echo "</ol>";
echo "<hr>";
echo "<a href=", $gobackURL,">戻る</a>";
exit();
}

//データベースユーザー
$user = 'test1';
$password = 'test10000';
//利用するデータベース
$dbName = 'inquiry_form';
//MySQLサーバ
$host = 'localhost:3306';
//MySQLのDSN文字列
$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/cotact.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link rel="icon" type="png" href="img/favicon.jpeg">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>お問い合わせフォーム | 京橋空手道場</title>
</head>

<body>
    <div class="header">
        <div id="logo"><a href="#"><img src="img/logo.png" alt="logo"></a></div>
        <nav id="main-nav">
            <ul id="main-menu">
                <li><a href="#">TOP</a></li>
                <li><a href="#">京橋空手道場について</a></li>
                <li><a href="#">ニュース</a></li>
                <li><a href="#">入会案内</a></li>
                <li><a href="#">よくある質問</a></li>
                <li><a href="#">WEB予約</a></li>
                <li><a href="#">お問い合わせ</a></li>
            </ul>
        </nav>
    </div>
    <div class="section-header">
        <div class="container">
            <div class="row">
                <h1 class="page-header-title">
                    お問い合わせ
                </h1>
            </div>
        </div>
        <?php
$name = $_POST["name"];
$furigana = $_POST["furigana"];
$tel = $_POST["tel"];
$email = $_POST["email"];
$message = $_POST["message"];
//MySQLデータベースに接続する
try{
$pdo = new PDO($dsn, $user, $password);
//プリペアドステートメントのエミュレーションを無効にする
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//例外がスローされる設定にする
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//SQL分を作る
$sql = "INSERT INTO data(name, furigana, tel, email, message)VALUES (:name,:furigana,:tel,:email,:message)";
//プリペアードステートメントを作る
$stm = $pdo->prepare($sql);
//プレースホルダーに値をバインドする
$stm->bindValue(':name',$name, PDO::PARAM_STR);
$stm->bindValue(':furigana', $furigana, PDO::PARAM_STR);
$stm->bindValue(':tel', $tel, PDO::PARAM_INT);
$stm->bindValue(':email', $email, PDO::PARAM_STR);
$stm->bindValue(':message', $message. PDO::PARAM_STR);
//SQL文を実行する
if($stm->execute()){
//レコード追加後のレコードリストを取得する
$sql = "SELECT * FROM data";
//プリペアドステートメントを作る
$stm = $pdo->prepare($sql);
//SQL文を実行する
$stm->execute();
//結果の取得
$result = $stm->fetchAll(PDO::FETCH_ASSOC);
//テーブルのタイトル行
echo "<table>";
echo "<thead><tr>";
echo "<th>", "ID", "</th>";
echo "<th>", "名前", "</th>";
echo "<th>", "ふりがな", "</th>";
echo "<th>", "電話番号", "</th>";
echo "<th>", "メールアドレス", "</th>";
echo "<th>", "お問い合わせ内容", "</th>";
echo "</tr><thead>";
//値を取り出して行に表示する
echo "<tbody>";
foreach ($result as $row){
//1行ずつテーブルに入れる
echo "<tr>";
echo "<td>", es($row['id']), "</td>";
echo "<td>", es($row['name']), "</td>";
echo "<td>", es($row['tel']), "</td>";
echo "<td>", es($row['email']), "</td>";
echo "<td>", es($row['message']), "</td>";
echo "</tr>";
}
echo "</tbody>";
echo "</table>";
} else {
echo '<span class="error">追加エラーがありました。</span><br>';
};
}catch (Exception $e){
echo '<span class="error">エラーがありました。</span><br>';
echo $e->getMessage();
}
?>
        <hr>
        <p><a href="<?php echo $gobackURL ?>">戻る</a></p>
    </div>


    </div>

    <footer class="section-site-footer">
        <div class="footer">
            <nav id="sub-nav">
                <ul id="sub-menu">
                    <li><a href="#">TOP</a></li>
                    <li><a href="#">京橋空手道場について</a></li>
                    <li><a href="#">ニュース</a></li>
                    <li><a href="#">入会案内</a></li>
                    <li><a href="#">よくある質問</a></li>
                    <li><a href="#">WEB予約</a></li>
                    <li><a href="#">お問い合わせ</a></li>
                </ul>
            </nav>
        </div>
    </footer>
    <div class="copy-box">
        <p>2021 Copyright© Kyobashi-Karate-Dojo All Rights Reserved</p>
    </div>
</body>

</html>