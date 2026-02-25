<?php

class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_identity;

    public function rules()
    {
        return [
            ['username, password', 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'authenticate'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'rememberMe' => 'Remember me',
        ];
    }

    public function authenticate($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $this->_identity = new UserIdentity($this->username, $this->password);
        if (!$this->_identity->authenticate()) {
            $this->addError('password', 'Incorrect username or password.');
        }
    }

    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }

        if ($this->_identity->errorCode !== UserIdentity::ERROR_NONE) {
            return false;
        }

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
        return Yii::app()->user->login($this->_identity, $duration);
    }
}
