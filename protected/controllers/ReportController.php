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
        $reportData = $this->getReportService()->buildTopAuthorsViewData(
            Yii::app()->request->getQuery('year')
        );

        if ($reportData['errorMessage'] !== null) {
            Yii::app()->user->setFlash('error', $reportData['errorMessage']);
        }

        $this->render('topAuthors', [
            'year' => $reportData['year'],
            'rows' => $reportData['rows'],
            'minYear' => $reportData['minYear'],
            'maxYear' => $reportData['maxYear'],
        ]);
    }

    protected function getReportService(): ReportService
    {
        return $this->getService('reportService', static function () {
            return new ReportService();
        });
    }
}
