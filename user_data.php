<?php

// userに関する関数
// ---------------------------------------

// validation
function validate_password($data)
{
    if (!isset($error_detail)) {
        $error_detail = [];
    }
    $param = ["pass_1", "pass_2"];
    foreach ($param as $val) {
        if ($data[$val] === "") {
            $error_detail["error_must_" . $val] = true;
        }
    }
    // passwordの長さチェック（72文字まで）
    if (mb_strlen($data["pass_1"]) > 72) {
        $error_detail["error_length_password"] = true;
    }
    // passwordとpassword(確認用)が同じか
    if ($data["pass_1"] !== $data["pass_2"]) {
        $error_detail["error_unmatch_password"] = true;
    }

    return $error_detail;
}

function validate_user($data)
{
    $error_detail = [];
    $param = ["name", "email"];
    foreach ($param as $val) {
        // 必須チェック
        if (!isset($data[$val]) || empty($data[$val])) {
            $error_detail["error_must_" . $val] = true;
        }
    }

    // emailの型チェック
    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        $error_detail["error_format_email"] = true;
    }

    // passwordのバリデートチェック
    $error_detail = validate_password($data);

    return $error_detail;
}

// passwordをhash化する関数
function pass_hash($pass)
{
    return password_hash($pass, PASSWORD_DEFAULT);
}
