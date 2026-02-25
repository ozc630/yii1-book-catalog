<?php

class UserIdentity extends CUserIdentity
{
    private $_id;

    public function authenticate()
    {
        $user = User::model()->findByAttributes(['username' => $this->username]);

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
            return false;
        }

        if (!$user->validatePassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
            return false;
        }

        $this->_id = (int) $user->id;
        $this->setState('role', $user->role);
        $this->errorCode = self::ERROR_NONE;

        return true;
    }

    public function getId()
    {
        return $this->_id;
    }
}
