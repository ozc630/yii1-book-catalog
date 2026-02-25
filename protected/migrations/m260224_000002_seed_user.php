<?php

class m260224_000002_seed_user extends CDbMigration
{
    public function safeUp()
    {
        $this->insert('users', [
            'username' => 'user',
            'password_hash' => '$2y$12$.qY7d7mUupg6SgayfGxQxOYtRrobv/cnBtjE7J7E7kCMOAsTvVJri',
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $now = date('Y-m-d H:i:s');

        $this->insert('authors', [
            'name' => 'George Orwell',
            'bio' => 'English novelist and essayist.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('authors', [
            'name' => 'Ray Bradbury',
            'bio' => 'American author and screenwriter.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('books', [
            'title' => '1984',
            'isbn' => '9780451524935',
            'published_year' => 1949,
            'cover_url' => 'https://example.com/1984.jpg',
            'description' => 'Dystopian social science fiction novel.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('book_author', [
            'book_id' => 1,
            'author_id' => 1,
        ]);
    }

    public function safeDown()
    {
        $this->delete('book_author', 'book_id=1 AND author_id=1');
        $this->delete('books', 'id=1');
        $this->delete('authors', 'id IN (1,2)');
        $this->delete('users', "username='user'");
    }
}
