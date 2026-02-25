<?php

declare(strict_types=1);

final class BookServiceTest extends IntegrationTestCase
{
    public function testCreateWithAuthorsPersistsRelationsAndCallsNotifier(): void
    {
        $author1 = $this->createAuthor();
        $author2 = $this->createAuthor();

        $notifier = new class {
            public array $calls = [];

            public function notifyNewBook(Book $book, array $authorIds): array
            {
                $this->calls[] = [
                    'book_id' => (int) $book->id,
                    'author_ids' => $authorIds,
                ];

                return [];
            }
        };

        $service = new BookService($this->db(), $notifier);

        $book = $service->createWithAuthors([
            'title' => 'TEST_BOOK_SERVICE_CREATE_' . $this->uniqueSuffix(),
            'isbn' => $this->generateIsbn(),
            'published_year' => $this->currentYear(),
            'cover_url' => 'https://example.com/service-create.jpg',
            'description' => 'BookService create test',
        ], [(int) $author1->id, (int) $author2->id]);

        self::assertFalse($book->hasErrors(), 'Book should be created without validation errors');
        self::assertNotEmpty($book->id, 'Created book must have ID');

        $this->trackBookId((int) $book->id);

        $authorIds = $this->db()->createCommand()
            ->select('author_id')
            ->from('book_author')
            ->where('book_id=:book_id', [':book_id' => (int) $book->id])
            ->order('author_id ASC')
            ->queryColumn();

        $authorIds = array_map('intval', $authorIds);
        $expectedAuthorIds = [(int) $author1->id, (int) $author2->id];
        sort($expectedAuthorIds);

        self::assertSame($expectedAuthorIds, $authorIds);
        self::assertCount(1, $notifier->calls, 'Notifier must be called once after successful create');

        $notifiedAuthorIds = array_map('intval', $notifier->calls[0]['author_ids']);
        sort($notifiedAuthorIds);

        self::assertSame($expectedAuthorIds, $notifiedAuthorIds);
        self::assertSame((int) $book->id, $notifier->calls[0]['book_id']);
    }

    public function testUpdateWithAuthorsReplacesRelations(): void
    {
        $author1 = $this->createAuthor();
        $author2 = $this->createAuthor();

        $service = new BookService($this->db(), new class {
            public function notifyNewBook(Book $book, array $authorIds): array
            {
                return [];
            }
        });

        $createdBook = $service->createWithAuthors([
            'title' => 'TEST_BOOK_SERVICE_UPDATE_' . $this->uniqueSuffix(),
            'isbn' => $this->generateIsbn(),
            'published_year' => $this->currentYear(),
            'cover_url' => 'https://example.com/service-update.jpg',
            'description' => 'Before update',
        ], [(int) $author1->id]);

        self::assertNotEmpty($createdBook->id);
        $this->trackBookId((int) $createdBook->id);

        $updatedBook = $service->updateWithAuthors((int) $createdBook->id, [
            'title' => 'TEST_BOOK_SERVICE_UPDATED_' . $this->uniqueSuffix(),
            'isbn' => $createdBook->isbn,
            'published_year' => $this->nextYear(),
            'cover_url' => 'https://example.com/service-updated.jpg',
            'description' => 'After update',
        ], [(int) $author2->id]);

        self::assertFalse($updatedBook->hasErrors());
        self::assertSame($this->nextYear(), (int) $updatedBook->published_year);

        $authorIds = $this->db()->createCommand()
            ->select('author_id')
            ->from('book_author')
            ->where('book_id=:book_id', [':book_id' => (int) $createdBook->id])
            ->queryColumn();

        self::assertSame([(int) $author2->id], array_map('intval', $authorIds));
    }

    public function testCreateWithAuthorsFailsWhenNoAuthorsPassed(): void
    {
        $service = new BookService($this->db(), new class {
            public function notifyNewBook(Book $book, array $authorIds): array
            {
                return [];
            }
        });

        $book = $service->createWithAuthors([
            'title' => 'TEST_BOOK_SERVICE_NO_AUTHORS_' . $this->uniqueSuffix(),
            'isbn' => $this->generateIsbn(),
            'published_year' => $this->currentYear(),
            'cover_url' => 'https://example.com/service-invalid.jpg',
            'description' => 'No authors validation case',
        ], []);

        self::assertTrue($book->hasErrors('authorIds'));
        self::assertEmpty($book->id);
    }
}
