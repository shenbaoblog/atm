<?php
class MoneyValidation extends BaseValidation
{
    // バリデーション（お金）
    public  function check($input) {
        if(!$input || !ctype_digit($input)) {
        $this->errors[] = '金額を正しく入力してください。';
            return false;
        }
        return true;
    }
}
