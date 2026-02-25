<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    private static bool $yiiBootstrapped = false;
    protected array $authorIds = [];
    protected array $bookIds = [];
    protected array $subscriptionIds = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootstrapYii();
    }

    protected function tearDown(): void
    {
        try {
            $this->deleteByIds('author_subscriptions', 'id', $this->subscriptionIds);
            $this->deleteByIds('books', 'id', $this->bookIds);
            $this->deleteByIds('authors', 'id', $this->authorIds);
        } catch (Throwable $e) {
            self::fail(sprintf(
                'Teardown failed: %s: %s at %s:%d%s%s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                PHP_EOL,
                $e->getTraceAsString()
            ));
        }

        parent::tearDown();
    }

    protected function db(): CDbConnection
    {
        return Yii::app()->db;
    }

    protected function createAuthor(?string $name = null): Author
    {
        $author = new Author();
        $author->name = $name ?? 'TEST_AUTHOR_' . $this->uniqueSuffix();
        $author->bio = 'Test author bio';

        if (!$author->save()) {
            self::fail('Unable to create author: ' . $this->collectErrors($author));
        }

        $this->authorIds[] = (int) $author->id;

        return $author;
    }

    protected function createBook(array $attributes = []): Book
    {
        $book = new Book();
        $book->title = $attributes['title'] ?? 'TEST_BOOK_' . $this->uniqueSuffix();
        $book->isbn = $attributes['isbn'] ?? $this->generateIsbn();
        $book->published_year = (int) ($attributes['published_year'] ?? $this->currentYear());
        $book->cover_url = $attributes['cover_url'] ?? 'https://example.com/test-cover.jpg';
        $book->description = $attributes['description'] ?? 'Test description';

        if (!$book->save()) {
            self::fail('Unable to create book: ' . $this->collectErrors($book));
        }

        $this->bookIds[] = (int) $book->id;

        return $book;
    }

    protected function createSubscription(int $authorId, string $phone): AuthorSubscription
    {
        $subscription = new AuthorSubscription();
        $subscription->author_id = $authorId;
        $subscription->phone = $phone;

        if (!$subscription->save()) {
            self::fail('Unable to create subscription: ' . $this->collectErrors($subscription));
        }

        $this->subscriptionIds[] = (int) $subscription->id;

        return $subscription;
    }

    protected function linkBookAuthor(int $bookId, int $authorId): void
    {
        $this->db()->createCommand()->insert('book_author', [
            'book_id' => $bookId,
            'author_id' => $authorId,
        ]);
    }

    protected function trackBookId(int $bookId): void
    {
        if ($bookId > 0) {
            $this->bookIds[] = $bookId;
        }
    }

    protected function uniqueSuffix(): string
    {
        return str_replace('.', '', uniqid('', true));
    }

    protected function generateIsbn(): string
    {
        $seed = preg_replace('/\D+/', '', (string) microtime(true) . (string) random_int(1000, 9999));
        return substr(str_pad($seed, 13, '7'), 0, 13);
    }

    protected function generatePhone(): string
    {
        $digits = preg_replace('/\D+/', '', (string) microtime(true) . (string) random_int(100000, 999999));
        return '+7' . substr(str_pad($digits, 10, '1'), 0, 10);
    }

    protected function currentYear(): int
    {
        return (int) date('Y');
    }

    protected function nextYear(): int
    {
        return (int) date('Y') + 1;
    }

    private function collectErrors(CModel $model): string
    {
        $messages = [];

        foreach ($model->getErrors() as $attributeMessages) {
            foreach ($attributeMessages as $message) {
                $messages[] = $message;
            }
        }

        return implode('; ', $messages);
    }

    private function deleteByIds(string $table, string $column, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $ids = array_values(array_unique(array_map('intval', $ids)));
        $placeholders = [];
        $params = [];

        foreach ($ids as $index => $id) {
            $placeholder = ':id' . $index;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $id;
        }

        $condition = sprintf('%s IN (%s)', $column, implode(', ', $placeholders));
        $this->db()->createCommand()->delete($table, $condition, $params);
    }

    private static function bootstrapYii(): void
    {
        if (self::$yiiBootstrapped) {
            return;
        }

        defined('YII_DEBUG') or define('YII_DEBUG', false);
        defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 0);

        $yii = dirname(__DIR__) . '/vendor/yiisoft/yii/framework/yii.php';
        $config = dirname(__DIR__) . '/protected/config/console.php';

        if (!file_exists($yii)) {
            self::fail('Yii framework is not installed. Run composer install.');
        }

        require_once $yii;

        if (Yii::app() === null) {
            Yii::createConsoleApplication($config);
        }

        Yii::app()->db->setActive(true);
        self::$yiiBootstrapped = true;
    }
}
