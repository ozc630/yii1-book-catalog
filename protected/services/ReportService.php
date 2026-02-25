<?php

class ReportService
{
    public function topAuthorsByYear(int $year, int $limit = 10): array
    {
        $year = (int) $year;
        $limit = max(1, (int) $limit);

        $command = Yii::app()->db->createCommand()
            ->select('a.id, a.name, COUNT(ba.book_id) AS books_count')
            ->from('authors a')
            ->join('book_author ba', 'ba.author_id = a.id')
            ->join('books b', 'b.id = ba.book_id')
            ->where('b.published_year = :year', [':year' => $year])
            ->group('a.id, a.name')
            ->order('books_count DESC, a.name ASC')
            ->limit($limit);

        $rows = $command->queryAll();

        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['books_count'] = (int) $row['books_count'];
        }

        return $rows;
    }
}
