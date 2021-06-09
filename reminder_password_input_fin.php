<?php

ob_start();
session_start();

require_once "./common_function.php";
require_once "./user_data.php";

// post送信された値を取得
$list = ["token", "pass_1", "pass_2"];
foreach ($list as $val) {
    $user_input_data[$val] = $_POST[$val];
}
// var_dump($user_input_data);

// トークンチェック
$dbh = get_dbh();
$sql = "SELECT * FROM reminder_token WHERE token = :token";
$pre = $dbh->prepare($sql);
$pre->bindValue(":token", $user_input_data["token"], PDO::PARAM_STR);
$r = $pre->execute();
if (!$r) {
    echo "システムエラーが起きました。";
    exit();
}
$token_data = [];
$token_data = $pre->fetch(PDO::FETCH_ASSOC);
// var_dump($token_data);
if (
    !isset($token_data["user_id"]) ||
    strtotime($token_data["created"]) + 3600 <= time()
) {
    $_SESSION["output_buffer"]["token"] = true;
    header(
        "Location: reminder_password_input.php?token=" .
            $user_input_data["token"]
    );
    exit();
}

unset($_SESSION["output_buffer"]);

// パスワードのバリデーション
$error_detail = [];
$error_detail = validate_password($user_input_data);
// var_dump($error_detail);
if (!empty($error_detail)) {
    $_SESSION["output_buffer"] = $error_detail;
    header(
        "Location: reminder_password_input.php?token=" .
            $user_input_data["token"]
    );
    exit();
}

// csrf_tokenチェック
if (!is_csrf_token()) {
    $_SESSION["output_buffer"]["csrf_token"] = true;
    header(
        "Location: reminder_password_input.php?token=" .
            $user_input_data["token"]
    );
    exit();
}

// 全てのチェックがOKだったので、パスワード変更処理を行う
$sql =
    "UPDATE users SET pass = :pass, updated = :updated WHERE user_id = :user_id";
$pre = $dbh->prepare($sql);
$pre->bindValue(":pass", pass_hash($user_input_data["pass_1"]), PDO::PARAM_STR);
$pre->bindValue(":updated", date("Y-m-d H:i:s"), PDO::PARAM_STR);
$pre->bindValue(":user_id", $token_data["user_id"], PDO::PARAM_STR);
$r = $pre->execute();
if (!$r) {
    echo "システムエラーが起きました。";
    exit();
}

// reminder_tokenの取得済みデータを削除
$sql = "DELETE FROM reminder_token WHERE token = :token";
$pre = $dbh->prepare($sql);
$pre->bindValue(":token", $token_data["token"], PDO::PARAM_STR);
$r = $pre->execute();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>DB講座　上級</title>
</head>
<body>
  <div class="container">
    <h5>パスワードを変更致しました。</h5>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
