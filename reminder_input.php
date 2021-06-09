<?php

ob_start();
session_start();

require_once "./common_function.php";
require_once "./user_data.php";

$error_detail = [];
if (isset($_SESSION["output_buffer"])) {
    $error_detail = $_SESSION["output_buffer"];
}
unset($_SESSION["output_buffer"]);

// トークンの発行
$csrf_token = create_csrf_token();
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
    <h4 style="margin-top: 50px; margin-bottom:20px;">パスワードリマインダー</h4>
    <?php if (isset($error_detail["csrf_token"])): ?>
      <span class="text-danger">CSRFトークンが異なります。トークンの有効期限は30分です。</span>
    <?php endif; ?>
    <form action="./reminder_input_fin.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo h(
          $csrf_token
      ); ?>">
      <p>送信頂いたemailにパスワード再設定の為のリマインダーを送信します。</p>
      email:<input type="text" class="form-control" name="email" value="">
      <input type="submit" class="btn btn-primary" style="margin-top: 20px;" value="email送信">
      <?php if (isset($error_detail["error_must_email"])): ?>
        <span class="text-danger">emailは必須項目です。</span>
      <?php endif; ?>
    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>