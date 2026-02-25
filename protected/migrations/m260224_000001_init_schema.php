<?php

class m260224_000001_init_schema extends CDbMigration
{
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->createTable('users', [
            'id' => 'pk',
            'username' => 'varchar(100) NOT NULL',
            'password_hash' => 'varchar(255) NOT NULL',
            'role' => "varchar(32) NOT NULL DEFAULT 'user'",
            'created_at' => 'datetime NOT NULL',
        ], $tableOptions);
        $this->createIndex('ux_users_username', 'users', 'username', true);

        $this->createTable('authors', [
            'id' => 'pk',
            'name' => 'varchar(255) NOT NULL',
            'bio' => 'text NULL',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ], $tableOptions);

        $this->createTable('books', [
            'id' => 'pk',
            'title' => 'varchar(255) NOT NULL',
            'isbn' => 'varchar(20) NOT NULL',
            'published_year' => 'smallint NOT NULL',
            'cover_url' => 'varchar(1024) NULL',
            'description' => 'text NULL',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ], $tableOptions);
        $this->createIndex('ux_books_isbn', 'books', 'isbn', true);
        $this->createIndex('ix_books_published_year', 'books', 'published_year');

        $this->createTable('book_author', [
            'book_id' => 'int NOT NULL',
            'author_id' => 'int NOT NULL',
            'PRIMARY KEY (`book_id`, `author_id`)',
        ], $tableOptions);

        $this->createTable('author_subscriptions', [
            'id' => 'pk',
            'author_id' => 'int NOT NULL',
            'phone' => 'varchar(32) NOT NULL',
            'created_at' => 'datetime NOT NULL',
        ], $tableOptions);
        $this->createIndex('ux_author_subscriptions_author_phone', 'author_subscriptions', 'author_id, phone', true);
        $this->createIndex('ix_author_subscriptions_phone', 'author_subscriptions', 'phone');

        $this->addForeignKey(
            'fk_book_author_book',
            'book_author',
            'book_id',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_book_author_author',
            'book_author',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_author_subscriptions_author',
            'author_subscriptions',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_author_subscriptions_author', 'author_subscriptions');
        $this->dropForeignKey('fk_book_author_author', 'book_author');
        $this->dropForeignKey('fk_book_author_book', 'book_author');

        $this->dropTable('author_subscriptions');
        $this->dropTable('book_author');
        $this->dropTable('books');
        $this->dropTable('authors');
        $this->dropTable('users');
    }
}
