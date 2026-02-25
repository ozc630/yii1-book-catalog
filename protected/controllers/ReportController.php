<?php

class ReportController extends Controller
{
    public function setReportService(ReportService $reportService): void
    {
        $this->setService('reportService', $reportService);
    }

    public function filters()
    {
        return ['accessControl'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['topAuthors'], 'users' => ['*']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionTopAuthors()
    {
        $requestedYear = Yii::app()->request->getQuery('year', date('Y'));
        $year = (int) $requestedYear;

        $minYear = 1450;
        $maxYear = (int) date('Y') + 1;

        if ($year < $minYear || $year > $maxYear) {
            Yii::app()->user->setFlash('error', sprintf('Year must be between %d and %d.', $minYear, $maxYear));
            $year = (int) date('Y');
        }

        $rows = $this->getReportService()->topAuthorsByYear($year, 10);

        $this->render('topAuthors', [
            'year' => $year,
            'rows' => $rows,
            'minYear' => $minYear,
            'maxYear' => $maxYear,
        ]);
    }

    protected function getReportService(): ReportService
    {
        return $this->getService('reportService', static function () {
            return new ReportService();
        });
    }
}
