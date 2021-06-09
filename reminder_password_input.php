<?php
ob_start();
session_start();

require_once "./common_function.php";
require_once "./user_data.php";

// getパラメーターのトークンを取得
$token = "";
if (isset($_GET["token"])) {
    $token = $_GET["token"];
}

// csrf_tokenを作成
$csrf_token = create_csrf_token();

// セッションのエラー情報取得
$error_detail = [];
if (isset($_SESSION["output_buffer"])) {
    $error_detail = $_SESSION["output_buffer"];
}
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
    <h4 style="margin-top: 50px; margin-bottom: 20px;">パスワード再設定</h4>
    <?php if (isset($error_detail["token"])): ?>
      <span class="text-danger">無効のトークンです。トークンの有効期限は60分です。</span>
    <?php endif; ?>
    <?php if (isset($error_detail["csrf_token"])): ?>
      <span class="text-danger">無効のCSRFトークンです。CSRFトークンの有効期限は5分です。</span>
    <?php endif; ?>
    <form action="./reminder_password_input_fin.php" method="POST">
      <input type="hidden" name="token" value="<?php echo h($token); ?>">
      <input type="hidden" name="csrf_token" value="<?php echo h(
          $csrf_token
      ); ?>">
      <?php if (
          isset($error_detail["error_must_pass_1"]) ||
          isset($error_detail["error_must_pass_2"])
      ): ?>
        <span class="text-danger">パスワードおよびパスワード(確認用)は必須です。<br></span>
      <?php endif; ?>
      <?php if (isset($error_detail["error_length_password"])): ?>
        <span class="text-danger">パスワードは72文字までです。<br></span>
      <?php endif; ?>
      <?php if (isset($error_detail["error_unmatch_password"])): ?>
        <span class="text-danger">パスワードとパスワード(確認用)が異なります。<br></span>
      <?php endif; ?>
      パスワード:<input type="password" name="pass_1" class="form-control" style="margin-bottom: 20px;">
      パスワード(確認用):<input type="password" name="pass_2" class="form-control" style="margin-bottom: 20px;">
      <input type="submit" class="btn btn-primary" value="パスワード変更" style="margin-bottom: 20px;">
    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>