<?php

class BookController extends Controller
{
    public function setBookService(BookService $bookService): void
    {
        $this->setService('bookService', $bookService);
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
        $this->render('index', $this->getBookService()->getIndexData());
    }

    public function actionView($id)
    {
        $book = $this->getBookService()->findBookWithAuthorsOrFail((int) $id);
        $this->render('view', ['book' => $book]);
    }

    public function actionCreate()
    {
        $bookService = $this->getBookService();
        $model = new Book();
        $selectedAuthorIds = [];

        $bookPost = Yii::app()->request->getPost('Book');
        if ($bookPost !== null) {
            $selectedAuthorIds = (array) Yii::app()->request->getPost('authorIds', []);
            $model = $bookService->createWithAuthors($bookPost, $selectedAuthorIds);

            if (!$model->hasErrors() && !empty($model->id)) {
                Yii::app()->user->setFlash('success', 'Book created successfully.');
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $model->authorIds = $selectedAuthorIds;
        $authors = $bookService->getAuthorsForForm();

        $this->render('create', [
            'model' => $model,
            'authors' => $authors,
            'selectedAuthorIds' => $selectedAuthorIds,
        ]);
    }

    public function actionUpdate($id)
    {
        $bookService = $this->getBookService();
        $model = $bookService->findBookWithAuthorsOrFail((int) $id);
        $selectedAuthorIds = CHtml::listData($model->authors, 'id', 'id');

        $bookPost = Yii::app()->request->getPost('Book');
        if ($bookPost !== null) {
            $selectedAuthorIds = (array) Yii::app()->request->getPost('authorIds', []);
            $model = $bookService->updateWithAuthors((int) $id, $bookPost, $selectedAuthorIds);

            if (!$model->hasErrors()) {
                Yii::app()->user->setFlash('success', 'Book updated successfully.');
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $model->authorIds = $selectedAuthorIds;
        $authors = $bookService->getAuthorsForForm();

        $this->render('update', [
            'model' => $model,
            'authors' => $authors,
            'selectedAuthorIds' => $selectedAuthorIds,
        ]);
    }

    public function actionDelete($id)
    {
        $bookService = $this->getBookService();
        $model = $bookService->findBookWithAuthorsOrFail((int) $id);
        $bookService->deleteBook($model);

        if (Yii::app()->request->getQuery('ajax') === null) {
            $this->redirect(['index']);
        }
    }

    protected function getBookService(): BookService
    {
        return $this->getService('bookService', static function () {
            return new BookService();
        });
    }
}
