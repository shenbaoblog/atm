<?php
class NameValidation extends BaseValidation
{
    // バリデーション（名前）
    public function check($input)
    {
        if (!$input) {
            $this->errors[] = '名前の形式では有りません。もう一度入力してください。';
            return false;
        }
        return true;
    }
}
