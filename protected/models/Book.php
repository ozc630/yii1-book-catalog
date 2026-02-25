<?php

class Book extends CActiveRecord
{
    public $authorIds = [];

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'books';
    }

    public function rules()
    {
        return [
            ['title, isbn, published_year', 'required'],
            ['title', 'length', 'max' => 255],
            ['isbn', 'length', 'max' => 20],
            ['isbn', 'match', 'pattern' => '/^[0-9Xx-]{10,20}$/', 'message' => 'ISBN format is invalid.'],
            ['isbn', 'unique'],
            ['published_year', 'numerical', 'integerOnly' => true, 'min' => 1450, 'max' => (int) date('Y') + 1],
            ['cover_url', 'url', 'allowEmpty' => true],
            ['cover_url', 'length', 'max' => 1024],
            ['description', 'safe'],
            ['authorIds', 'safe'],
            ['id, title, isbn, published_year', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'authors' => [self::MANY_MANY, 'Author', 'book_author(book_id, author_id)'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'isbn' => 'ISBN',
            'published_year' => 'Published Year',
            'cover_url' => 'Cover URL',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'authorIds' => 'Authors',
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

    public function getAuthorNames()
    {
        if (empty($this->authors)) {
            return '';
        }

        $names = [];
        foreach ($this->authors as $author) {
            $names[] = $author->name;
        }

        return implode(', ', $names);
    }
}
