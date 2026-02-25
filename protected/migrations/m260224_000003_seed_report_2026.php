<?php

class m260224_000003_seed_report_2026 extends CDbMigration
{
    private const REPORT_YEAR = 2026;
    private const AUTHOR_PREFIX = 'Seed Report 2026 Author';
    private const BOOK_PREFIX = 'SEED_REPORT_2026_BOOK';

    public function safeUp()
    {
        $now = date('Y-m-d H:i:s');
        $bookCountsByAuthor = [12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

        foreach ($bookCountsByAuthor as $authorIndex => $bookCount) {
            $authorNumber = $authorIndex + 1;
            $authorName = sprintf('%s %02d', self::AUTHOR_PREFIX, $authorNumber);

            $this->insert('authors', [
                'name' => $authorName,
                'bio' => sprintf('Seed data for TOP-10 report (%d).', self::REPORT_YEAR),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $authorId = (int) $this->dbConnection->getLastInsertID();

            for ($bookIndex = 1; $bookIndex <= $bookCount; $bookIndex++) {
                $bookTitle = sprintf('%s_A%02d_B%02d', self::BOOK_PREFIX, $authorNumber, $bookIndex);
                $isbn = sprintf('9782026%02d%05d', $authorNumber, $bookIndex);

                $this->insert('books', [
                    'title' => $bookTitle,
                    'isbn' => $isbn,
                    'published_year' => self::REPORT_YEAR,
                    'cover_url' => 'https://example.com/seed-report-cover.jpg',
                    'description' => sprintf('Seed book for report year %d.', self::REPORT_YEAR),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $bookId = (int) $this->dbConnection->getLastInsertID();

                $this->insert('book_author', [
                    'book_id' => $bookId,
                    'author_id' => $authorId,
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->delete('books', 'title LIKE :titlePrefix', [
            ':titlePrefix' => self::BOOK_PREFIX . '%',
        ]);

        $this->delete('authors', 'name LIKE :authorPrefix', [
            ':authorPrefix' => self::AUTHOR_PREFIX . '%',
        ]);
    }
}
