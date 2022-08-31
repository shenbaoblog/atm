<?php
class IdExistValidation extends BaseValidation
{
    public function check($input_id)
    {
        $id_exist_bool = false;
        // Userクラスのユーザーリストにidがあるかチェック
        foreach (User::$user_list as $key => $value) {
            if ($value['id'] == $input_id) {
                $id_exist_bool = true;
            }
        }

        // なければエラー、再帰関数
        if ($id_exist_bool === false) {
            $this->errors[] = '存在しないIDです。IDを再入力してください。';
            return false;
        }
        return true;
    }
}
