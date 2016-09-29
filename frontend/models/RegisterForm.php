<?php
namespace frontend\models;

use common\helpers\Helper;
use common\models\Model;
use common\models\User;

class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $repassword;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'password', 'repassword'], 'required'],
            [['username', 'email'], 'string', 'min' => 2, 'max' => 100],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户邮箱必须唯一'],
            ['password', 'string', 'min' => 6, 'max' => 30],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function register()
    {
        $mixReturn = null;
        if ($this->validate()) {
            $user = new User();
            $user->username  = $this->username;
            $user->email     = $this->email;
            $user->last_time = time();
            $user->last_ip   = Helper::getIpAddress();
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) $mixReturn = $user;
        }

        return $mixReturn;
    }
}
