<?php

class BookAuthor extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'book_author';
    }

    public function rules()
    {
        return [
            ['book_id, author_id', 'required'],
            ['book_id, author_id', 'numerical', 'integerOnly' => true],
        ];
    }

    public function relations()
    {
        return [
            'book' => [self::BELONGS_TO, 'Book', 'book_id'],
            'author' => [self::BELONGS_TO, 'Author', 'author_id'],
        ];
    }
}
