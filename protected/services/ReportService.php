<?php

class ReportService
{
    public const DEFAULT_TOP_AUTHORS_LIMIT = 10;

    public function buildTopAuthorsViewData($requestedYear, int $limit = self::DEFAULT_TOP_AUTHORS_LIMIT): array
    {
        $yearState = $this->resolveTopAuthorsYear($requestedYear);

        return [
            'year' => $yearState['year'],
            'rows' => $this->topAuthorsByYear($yearState['year'], $limit),
            'minYear' => $yearState['minYear'],
            'maxYear' => $yearState['maxYear'],
            'errorMessage' => $yearState['errorMessage'],
        ];
    }

    public function topAuthorsByYear(int $year, int $limit = self::DEFAULT_TOP_AUTHORS_LIMIT): array
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

    private function resolveTopAuthorsYear($requestedYear): array
    {
        $currentYear = (int) date('Y');
        $minYear = 1450;
        $maxYear = $currentYear + 1;

        $rawYear = trim((string) $requestedYear);

        if ($rawYear === '') {
            return [
                'year' => $currentYear,
                'minYear' => $minYear,
                'maxYear' => $maxYear,
                'errorMessage' => null,
            ];
        }

        if (!preg_match('/^\d{4}$/', $rawYear)) {
            return [
                'year' => $currentYear,
                'minYear' => $minYear,
                'maxYear' => $maxYear,
                'errorMessage' => sprintf('Year must be a 4-digit number between %d and %d.', $minYear, $maxYear),
            ];
        }

        $year = (int) $rawYear;
        if ($year < $minYear || $year > $maxYear) {
            return [
                'year' => $currentYear,
                'minYear' => $minYear,
                'maxYear' => $maxYear,
                'errorMessage' => sprintf('Year must be between %d and %d.', $minYear, $maxYear),
            ];
        }

        return [
            'year' => $year,
            'minYear' => $minYear,
            'maxYear' => $maxYear,
            'errorMessage' => null,
        ];
    }
}
