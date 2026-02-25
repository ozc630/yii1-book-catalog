<?php

declare(strict_types=1);

final class ReportServiceTest extends IntegrationTestCase
{
    public function testTopAuthorsByYearReturnsSortedAndLimitedRows(): void
    {
        $service = new ReportService();
        $year = max(1450, $this->currentYear() - 3);

        $expectedNames = [];

        for ($i = 1; $i <= 12; $i++) {
            $author = $this->createAuthor(sprintf('TEST_REPORT_%s_%02d', $this->uniqueSuffix(), $i));
            $book = $this->createBook([
                'title' => sprintf('TEST_REPORT_BOOK_%02d_%s', $i, $this->uniqueSuffix()),
                'published_year' => $year,
            ]);
            $this->linkBookAuthor((int) $book->id, (int) $author->id);

            $expectedNames[] = $author->name;
        }

        sort($expectedNames);
        $expectedTop10 = array_slice($expectedNames, 0, 10);

        $rows = $service->topAuthorsByYear($year, 10);

        self::assertCount(10, $rows);
        self::assertSame($expectedTop10, array_column($rows, 'name'));

        foreach ($rows as $row) {
            self::assertSame(1, (int) $row['books_count']);
        }
    }

    public function testTopAuthorsByYearFiltersByRequestedYear(): void
    {
        $service = new ReportService();
        $yearA = max(1450, $this->currentYear() - 3);
        $yearB = min($this->nextYear(), $yearA + 1);

        $author = $this->createAuthor('TEST_REPORT_FILTER_' . $this->uniqueSuffix());

        $bookYearA = $this->createBook([
            'title' => 'TEST_REPORT_FILTER_A_' . $this->uniqueSuffix(),
            'published_year' => $yearA,
        ]);
        $this->linkBookAuthor((int) $bookYearA->id, (int) $author->id);

        $bookYearB = $this->createBook([
            'title' => 'TEST_REPORT_FILTER_B_' . $this->uniqueSuffix(),
            'published_year' => $yearB,
        ]);
        $this->linkBookAuthor((int) $bookYearB->id, (int) $author->id);

        $rowsYearA = $service->topAuthorsByYear($yearA, 10);
        $rowsYearB = $service->topAuthorsByYear($yearB, 10);

        $rowYearA = $this->findRowByAuthorId($rowsYearA, (int) $author->id);
        $rowYearB = $this->findRowByAuthorId($rowsYearB, (int) $author->id);

        self::assertNotNull($rowYearA);
        self::assertNotNull($rowYearB);
        self::assertSame(1, (int) $rowYearA['books_count']);
        self::assertSame(1, (int) $rowYearB['books_count']);
    }

    private function findRowByAuthorId(array $rows, int $authorId): ?array
    {
        foreach ($rows as $row) {
            if ((int) $row['id'] === $authorId) {
                return $row;
            }
        }

        return null;
    }
}
