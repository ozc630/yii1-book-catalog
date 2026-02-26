<?php

class AuthorService
{
    public const DEFAULT_INDEX_PAGE_SIZE = 10;

    public function getIndexData(int $pageSize = self::DEFAULT_INDEX_PAGE_SIZE): array
    {
        $criteria = $this->buildIndexCriteria();
        $pages = new CPagination($this->countAuthors());
        $pages->pageSize = max(1, (int) $pageSize);
        $pages->applyLimit($criteria);

        return [
            'authors' => $this->findAuthorsForIndex($criteria),
            'pages' => $pages,
        ];
    }

    public function saveAuthor(Author $model, array $attributes): bool
    {
        $model->attributes = $attributes;

        return $model->save();
    }

    public function findAuthorWithBooksOrFail(int $id): Author
    {
        $model = Author::model()->with('books')->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'Author not found.');
        }

        return $model;
    }

    public function deleteAuthor(Author $model): void
    {
        $model->delete();
    }

    private function buildIndexCriteria(): CDbCriteria
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id, t.name, COUNT(ba.book_id) AS books_count';
        $criteria->join = 'LEFT JOIN book_author ba ON ba.author_id = t.id';
        $criteria->group = 't.id, t.name';
        $criteria->order = 't.name ASC';

        return $criteria;
    }

    private function findAuthorsForIndex(CDbCriteria $criteria): array
    {
        return Author::model()->findAll($criteria);
    }

    private function countAuthors(): int
    {
        return (int) Author::model()->count();
    }
}
