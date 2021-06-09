<?php

ob_start();
session_start();

require_once "./common_function.php";
require_once "./user_data.php";

$param = ["name", "email", "pass_1", "pass_2"];
$user_input = [];
foreach ($param as $val) {
    if (isset($_POST[$val])) {
        $user_input[$val] = $_POST[$val];
    }
}
// var_dump($user_input);

// validationチェック
$error_detail = [];
$error_detail = validate_user($user_input);

// トークンの確認
if (!is_csrf_token()) {
    $error_detail["csrf_token"] = true;
}

if (!empty($error_detail)) {
    // セッションに必要な情報を詰める
    $_SESSION["output_buffer"]["user_input"] = $user_input;
    $_SESSION["output_buffer"]["error_detail"] = $error_detail;
    header("Location: user_register.php");
    exit();
}
unset($_SESSION["output_buffer"]);

// DBにInsert
$dbh = get_dbh();
$sql =
    "INSERT INTO users (name, email, pass, created, updated) VALUES (:name, :email, :pass, :created, :updated)";
$pre = $dbh->prepare($sql);
// bind
$pre->bindValue(":name", $user_input["name"], PDO::PARAM_STR);
$pre->bindValue(":email", $user_input["email"], PDO::PARAM_STR);
$pre->bindValue(":pass", pass_hash($user_input["pass_1"]), PDO::PARAM_STR);
$pre->bindValue(":created", date("Y-m-d H:i:s"), PDO::PARAM_STR);
$pre->bindValue(":updated", date("Y-m-d H:i:s"), PDO::PARAM_STR);

$r = $pre->execute();
if (!$r) {
    echo "システムエラーが起こりました。";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB講座 上級</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <p>ユーザー登録ありがとうございます。</p>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>