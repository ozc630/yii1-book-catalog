<?php

class BookService
{
    private $db;
    private $subscriptionNotifier;

    public function __construct($db = null, $subscriptionNotifier = null)
    {
        $this->db = $db ?: Yii::app()->db;
        $this->subscriptionNotifier = $subscriptionNotifier ?: new SubscriptionNotifier();
    }

    public function createWithAuthors(array $bookData, array $authorIds)
    {
        $book = new Book();
        $book->attributes = $bookData;

        $authorIds = $this->normalizeAuthorIds($authorIds);
        $isValid = $this->validateBookPayload($book, $authorIds);

        if (!$isValid) {
            return $book;
        }

        $transaction = $this->db->beginTransaction();

        try {
            if (!$book->save(false)) {
                throw new CException('Unable to save book');
            }

            $this->syncBookAuthors((int) $book->id, $authorIds);
            $transaction->commit();

            $bookWithAuthors = Book::model()->with('authors')->findByPk($book->id);
            if ($bookWithAuthors !== null) {
                $book = $bookWithAuthors;
                $this->subscriptionNotifier->notifyNewBook($bookWithAuthors, $authorIds);
            }

            return $book;
        } catch (Throwable $e) {
            if ($transaction->getActive()) {
                $transaction->rollback();
            }

            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'book.service');
            $book->addError('title', 'Unable to save book. Please try again.');

            return $book;
        }
    }

    public function updateWithAuthors(int $bookId, array $bookData, array $authorIds)
    {
        $book = Book::model()->findByPk((int) $bookId);

        if ($book === null) {
            throw new CHttpException(404, 'Book not found.');
        }

        $book->attributes = $bookData;

        $authorIds = $this->normalizeAuthorIds($authorIds);
        $isValid = $this->validateBookPayload($book, $authorIds);

        if (!$isValid) {
            return $book;
        }

        $transaction = $this->db->beginTransaction();

        try {
            if (!$book->save(false)) {
                throw new CException('Unable to update book');
            }

            $this->syncBookAuthors((int) $book->id, $authorIds);
            $transaction->commit();

            $bookWithAuthors = Book::model()->with('authors')->findByPk($book->id);
            if ($bookWithAuthors !== null) {
                return $bookWithAuthors;
            }

            return $book;
        } catch (Throwable $e) {
            if ($transaction->getActive()) {
                $transaction->rollback();
            }

            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'book.service');
            $book->addError('title', 'Unable to update book. Please try again.');

            return $book;
        }
    }

    private function validateBookPayload(Book $book, array $authorIds): bool
    {
        $isValid = $book->validate();

        if (empty($authorIds)) {
            $book->addError('authorIds', 'Select at least one author.');
            $isValid = false;
        }

        if (!$this->authorsExist($authorIds)) {
            $book->addError('authorIds', 'One or more selected authors do not exist.');
            $isValid = false;
        }

        return $isValid;
    }

    private function authorsExist(array $authorIds): bool
    {
        if (empty($authorIds)) {
            return false;
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $authorIds);

        $count = (int) Author::model()->count($criteria);

        return $count === count($authorIds);
    }

    private function syncBookAuthors(int $bookId, array $authorIds): void
    {
        $this->db->createCommand()->delete('book_author', 'book_id=:book_id', [':book_id' => (int) $bookId]);

        foreach ($authorIds as $authorId) {
            $this->db->createCommand()->insert('book_author', [
                'book_id' => (int) $bookId,
                'author_id' => (int) $authorId,
            ]);
        }
    }

    private function normalizeAuthorIds(array $authorIds): array
    {
        $normalized = [];
        foreach ($authorIds as $authorId) {
            $authorId = (int) $authorId;
            if ($authorId > 0) {
                $normalized[] = $authorId;
            }
        }

        return array_values(array_unique($normalized));
    }
}
