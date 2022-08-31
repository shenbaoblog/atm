<?php

// ATMを作成しよう
// コマンドラインから実行すること

// 要件定義
// ・残額、入金、引き出しの機能を実装

// 実際にATMに必要な機能をリストアップして、ご自由に開発してみてください！


// 要件定義

// 名前
// パスワード
// 口座番号
// 支店番号
// 残高確認
// 入金
// 出金

// 入出金時だけ、数字バリデーション
// 入出金時だけ、パスワード入力を求める（他の作業時は、毎回入力するのが面倒なので、省略）




include './validations/BaseValidation.php';
include './validations/IdExistValidation.php';
include './validations/ModeValidation.php';
include './validations/MoneyValidation.php';
include './validations/NameValidation.php';
include './validations/PasswordValidation.php';



class User
{
    public static $user_list = array(
        1 => array(
            "id" => "1",
            "password" => "1234",
            "name" => "tanaka",
            "balance" => "500000",
        ),
        2 => array(
            "id" => "2",
            "password" => "3456",
            "name" => "suzuki",
            "balance" => "1000000",
        ),
    );

    public static function getUserList()
    {
        return self::$user_list;
    }


    public static function getUserById($id)
    {
        return self::$user_list[$id];
    }
}


class AtmAccount
{
    private $user;
    private $error_count = 0;

    // モード選択用変数
    const MODE = array(
        'PAYMENT' => '1',
        'WITHDRAW' => '2',
        'BALANCE_DISPLAY' => '3',
        'NAME_CHANGE' => '4',
        'NAME_DISPLAY' => '5',
        'PASSWORD_CHANGE' => '6',
        'PASSWORD_DISPLAY' => '7',
        'EXIT' => '99',
    );

    // バリデーションモード選択用
    const VALID_MODE = array(
        'ID' => 'ID',
        'MODE' => 'MODE',
        'MONEY' => 'MONEY',
        'NAME' => 'NAME',
        'PASSWORD' => 'PASSWORD',
    );

    // パスワード入力可能回数
    const MAX_PASS_ERROR = 3;


    function __construct()
    {
        $this->idExistValid = new IdExistValidation();
        $this->modeValid = new ModeValidation();
        $this->moneyValid = new MoneyValidation();
        $this->nameValid = new NameValidation();
        $this->passValid = new PasswordValidation();

        // ログイン
        $this->login();
    }


    // ログイン
    public function login()
    {
        // id入力
        echo "IDを入力してください。" . PHP_EOL;
        echo "ID:";
        $input_id = trim(fgets(STDIN));

        // Userクラスのユーザーリストにidがあるかチェック
        $validation = new IdExistValidation();
        if ($validation->check($input_id) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->login();
        }

        // Userクラスから指定されたユーザー取得
        $user = User::getUserById((int)$input_id);

        // パスワード取得
        $this->certification($user);

        $this->user = $user;
    }



    // メイン処理
    public function main()
    {
        echo PHP_EOL;
        echo '1:入金' . PHP_EOL;
        echo '2:出金' . PHP_EOL;
        echo '3:残高紹介' . PHP_EOL;
        echo '4:名前変更' . PHP_EOL;
        echo '5:名前表示' . PHP_EOL;
        echo '6:パスワード変更' . PHP_EOL;
        echo '7:パスワード表示' . PHP_EOL;
        echo '99:終了' . PHP_EOL;

        echo 'モード選択:';

        $input_mode = trim(fgets(STDIN));

        // モード選択バリデーション
        $validation = new ModeValidation();
        if ($validation->check($input_mode) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->main();
        }


        switch ($input_mode) {
            case self::MODE['PAYMENT']:
                $this->payment();
                break;
            case self::MODE['WITHDRAW']:
                $this->withdraw();
                break;
            case self::MODE['BALANCE_DISPLAY']:
                echo $this->user['balance'] . PHP_EOL;
                break;
            case self::MODE['NAME_CHANGE']:
                $this->inputName();
                break;
            case self::MODE['NAME_DISPLAY']:
                echo $this->user['name'] . PHP_EOL;
                break;
            case self::MODE['PASSWORD_CHANGE']:
                $this->inputPassword();
                break;
            case self::MODE['PASSWORD_DISPLAY']:
                echo $this->user['password'] . PHP_EOL;
                break;
            case self::MODE['EXIT']:
                return;
        }
        $this->main();
    }



    // 認証
    private function certification($user)
    {
        echo 'パスワードを入力してください:';
        $input_password = trim(fgets(STDIN));



        if ($user['password'] === $input_password) {
            $this->error_count = 0;
            return true;
        }

        echo 'パスワード入力間違い' . ++$this->error_count . '回目' . PHP_EOL;
        if ($this->error_count >= self::MAX_PASS_ERROR) {
            $this->error_count = 0;
            exit();
        }
        echo 'パスワードが間違っています';
        return $this->certification($user);
    }



    // 入金
    private function payment()
    {
        echo '入金額を入力:';
        $input_money = trim(fgets(STDIN));

        $validation = new MoneyValidation();
        if ($validation->check($input_money) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->payment();
        }

        if ($this->certification($this->user)) {
            $this->user['balance'] = $this->user['balance'] + $input_money;
        }
    }



    // 出金
    private function withdraw()
    {
        echo '出金額を入力:';
        $input_money = trim(fgets(STDIN));

        $validation = new MoneyValidation();
        if ($validation->check($input_money) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->withdraw();
        }

        if ($this->certification($this->user)) {
            $this->user['balance'] = $this->user['balance'] - $input_money;
        }
    }


    // 名前の変更
    private function inputName()
    {
        echo '名前を変更:' . PHP_EOL;
        echo '新しい名前を入力:';
        $input_name = trim(fgets(STDIN));

        $validation = new NameValidation();
        if ($validation->check($input_name) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->inputName();
        }

        $this->user['name'] = $input_name;
    }


    // パスワードの変更
    private function inputPassword()
    {
        echo 'パスワードを変更:' . PHP_EOL;
        echo '新しいパスワードを入力:';
        $input_password = trim(fgets(STDIN));

        $validation = new PasswordValidation();
        if ($validation->check($input_password) === false) {
            $errors = $validation->getErrorMessages();
            $this->outputErrorMsg($errors);

            return $this->inputPassword();
        }

        $this->user['password'] = $input_password;
    }


    // エラーメッセージ出力用メソッド
    private function outputErrorMsg($errors)
    {
        foreach ($errors as $errorMsg) {
            echo $errorMsg . PHP_EOL;
        }
    }
}




$atm = new AtmAccount();


$atm->main();


// OKです！これでATMもOKとしますね。
// エラーメッセージを定数管理にすると、よりベターかと思います。
// オブジェクト指向の基本を実践することができたかなと思います！
