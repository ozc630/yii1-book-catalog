<?php

class AuthorController extends Controller
{
    public function setAuthorService(AuthorService $authorService): void
    {
        $this->setService('authorService', $authorService);
    }

    public function filters()
    {
        return ['accessControl', 'postOnly + delete'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['index', 'view'], 'users' => ['*']],
            ['allow', 'actions' => ['create', 'update', 'delete'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionIndex()
    {
        $this->render('index', $this->getAuthorService()->getIndexData());
    }

    public function actionView($id)
    {
        $author = $this->getAuthorService()->findAuthorWithBooksOrFail((int) $id);
        $this->render('view', ['author' => $author]);
    }

    public function actionCreate()
    {
        $model = new Author();
        $authorService = $this->getAuthorService();

        $authorPost = Yii::app()->request->getPost('Author');
        if ($authorPost !== null && $authorService->saveAuthor($model, $authorPost)) {
            $this->redirect(['view', 'id' => $model->id]);
            return;
        }

        $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $authorService = $this->getAuthorService();
        $model = $authorService->findAuthorWithBooksOrFail((int) $id);

        $authorPost = Yii::app()->request->getPost('Author');
        if ($authorPost !== null && $authorService->saveAuthor($model, $authorPost)) {
            $this->redirect(['view', 'id' => $model->id]);
            return;
        }

        $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $authorService = $this->getAuthorService();
        $model = $authorService->findAuthorWithBooksOrFail((int) $id);
        $authorService->deleteAuthor($model);

        if (Yii::app()->request->getQuery('ajax') === null) {
            $this->redirect(['index']);
        }
    }

    protected function getAuthorService(): AuthorService
    {
        return $this->getService('authorService', static function () {
            return new AuthorService();
        });
    }
}
