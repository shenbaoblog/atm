<?php
class PasswordValidation extends BaseValidation
{
    // バリデーション（パスワード）
    public function check($input)
    {
        if (!$input || !(mb_strlen($input) == mb_strwidth($input))) {
            $this->errors[] = 'パスワードの形式では有りません。もう一度入力してください';
            return false;
        }
        return true;
    }
}
