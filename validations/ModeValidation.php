<?php
class ModeValidation extends BaseValidation
{

    // バリデーション（モード）
    public function check($input)
    {
        $errorMsg = 'エラー：有効な数字を入力してください。';

        if (!$input) {
            $this->errors[] = $errorMsg;
            return false;
        }

        $mode_exist_bool = false;
        foreach (AtmAccount::MODE as $mode) {
            if ($input === $mode) {
                $mode_exist_bool  = true;
            }
        }

        if ($mode_exist_bool === false) {
            $this->errors[] = $errorMsg;
            return $mode_exist_bool;
        }
        return $mode_exist_bool;
    }
}
