<?php

class Author extends CActiveRecord
{
    public $books_count = 0;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'authors';
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'length', 'max' => 255],
            ['bio', 'safe'],
            ['id, name, bio', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'books' => [self::MANY_MANY, 'Book', 'book_author(author_id, book_id)'],
            'subscriptions' => [self::HAS_MANY, 'AuthorSubscription', 'author_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'bio' => 'Bio',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    protected function beforeSave()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->isNewRecord) {
            $this->created_at = $now;
        }

        $this->updated_at = $now;

        return parent::beforeSave();
    }
}
