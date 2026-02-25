<?php

class User extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['username, password_hash, role', 'required'],
            ['username', 'length', 'max' => 100],
            ['password_hash', 'length', 'max' => 255],
            ['role', 'length', 'max' => 32],
            ['username', 'unique'],
            ['id, username, role, created_at', 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'created_at' => 'Created At',
        ];
    }

    public function validatePassword($password)
    {
        return password_verify($password, $this->password_hash);
    }
}
