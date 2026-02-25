<?php

class AuthorSubscription extends CActiveRecord
{
    private $duplicateConstraintViolation = false;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'author_subscriptions';
    }

    public function rules()
    {
        return [
            ['author_id, phone', 'required'],
            ['author_id', 'numerical', 'integerOnly' => true],
            ['phone', 'match', 'pattern' => '/^\+?[1-9]\d{10,14}$/', 'message' => 'Phone must be in international format, for example +79991234567'],
            ['phone', 'length', 'max' => 32],
            ['id, author_id, phone, created_at', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'author' => [self::BELONGS_TO, 'Author', 'author_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author',
            'phone' => 'Phone',
            'created_at' => 'Created At',
        ];
    }

    protected function beforeValidate()
    {
        if (is_string($this->phone)) {
            $this->phone = trim($this->phone);
        }

        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave();
    }

    public function saveIdempotent(): bool
    {
        $this->duplicateConstraintViolation = false;

        try {
            return (bool) $this->save();
        } catch (CDbException $exception) {
            if ($this->isDuplicateKeyException($exception)) {
                $this->duplicateConstraintViolation = true;
                return false;
            }

            throw $exception;
        }
    }

    public function hasDuplicateConstraintViolation(): bool
    {
        return $this->duplicateConstraintViolation;
    }

    private function isDuplicateKeyException(CDbException $exception): bool
    {
        $message = $exception->getMessage();

        return stripos($message, 'SQLSTATE[23000]') !== false
            || stripos($message, '1062') !== false
            || stripos($message, 'duplicate entry') !== false;
    }
}
