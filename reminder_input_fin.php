<?php

ob_start();
session_start();

require_once "./common_function.php";
require_once "./user_data.php";

// 必須チェック
$error_detail = [];
if ($_POST["email"] === "") {
    $_SESSION["output_buffer"]["error_must_email"] = true;
}

// トークンチェック
if (!is_csrf_token()) {
    $_SESSION["output_buffer"]["csrf_token"] = true;
}

if (!empty($error_detail)) {
    header("Location: reminder_input.php");
    exit();
}

// 送信されたemailがusersテーブルにあるか確認
$dbh = get_dbh();
$sql = "SELECT user_id from users WHERE email = :email";
$pre = $dbh->prepare($sql);

// bind
$pre->bindValue(":email", $_POST["email"], PDO::PARAM_STR);
$r = $pre->execute();
if (!$r) {
    echo "システムエラーが発生しました。";
    exit();
}

$id = $pre->fetch(PDO::FETCH_ASSOC)["user_id"];
// var_dump($id);

$mail_url = "";

if ($id) {
    $token = hash("sha512", random_bytes(128));
    $mail_url =
        "http://localhost/udemy_php_database_advance/my_learning/reminder_password_input.php?token=" .
        $token;
    $sql =
        "INSERT INTO reminder_token (token, user_id, created) VALUES (:token, :user_id, :created)";
    $pre = $dbh->prepare($sql);

    $pre->bindValue(":token", $token, PDO::PARAM_STR);
    $pre->bindValue(":user_id", $id, PDO::PARAM_STR);
    $pre->bindValue(":created", date("Y-m-d H:i:s"));

    $r = $pre->execute();
    if (!$r) {
        echo "システムエラーが起こりました。";
        exit();
    }
}

var_dump($mail_url);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>DB講座 上級</title>
</head>
<body>
  <div class="container">
    <h5 style="margin-top: 50px; margin-bottom: 20px;">指定のemailにurlリンクをお送りしました。リンクにアクセスして下さい。</h5>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>