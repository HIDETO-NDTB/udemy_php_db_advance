<?php

// userに関する関数
// ---------------------------------------

// validation
function validate_user($data)
{
    $error_detail = [];
    $param = ["name", "email", "password_1", "password_2"];
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

    // passwordの長さチェック（72文字まで）
    if (mb_strlen($data["password_1"]) > 72) {
        $error_detail["error_length_password"] = true;
    }
    // passwordとpassword(確認用)が同じか
    if ($data["password_1"] !== $data["password_2"]) {
        $error_detail["error_unmatch_password"] = true;
    }
    return $error_detail;
}
