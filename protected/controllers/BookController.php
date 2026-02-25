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
        $books = Book::model()->with('authors')->findAll(['order' => 't.created_at DESC']);
        $this->render('index', ['books' => $books]);
    }

    public function actionView($id)
    {
        $book = $this->loadModel($id);
        $this->render('view', ['book' => $book]);
    }

    public function actionCreate()
    {
        $model = new Book();
        $selectedAuthorIds = [];

        $bookPost = Yii::app()->request->getPost('Book');
        if ($bookPost !== null) {
            $selectedAuthorIds = (array) Yii::app()->request->getPost('authorIds', []);
            $model = $this->getBookService()->createWithAuthors($bookPost, $selectedAuthorIds);

            if (!$model->hasErrors() && !empty($model->id)) {
                Yii::app()->user->setFlash('success', 'Book created successfully.');
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $model->authorIds = $selectedAuthorIds;
        $authors = Author::model()->findAll(['order' => 'name ASC']);

        $this->render('create', [
            'model' => $model,
            'authors' => $authors,
            'selectedAuthorIds' => $selectedAuthorIds,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        $selectedAuthorIds = CHtml::listData($model->authors, 'id', 'id');

        $bookPost = Yii::app()->request->getPost('Book');
        if ($bookPost !== null) {
            $selectedAuthorIds = (array) Yii::app()->request->getPost('authorIds', []);
            $model = $this->getBookService()->updateWithAuthors((int) $id, $bookPost, $selectedAuthorIds);

            if (!$model->hasErrors()) {
                Yii::app()->user->setFlash('success', 'Book updated successfully.');
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $model->authorIds = $selectedAuthorIds;
        $authors = Author::model()->findAll(['order' => 'name ASC']);

        $this->render('update', [
            'model' => $model,
            'authors' => $authors,
            'selectedAuthorIds' => $selectedAuthorIds,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $model->delete();

        if (Yii::app()->request->getQuery('ajax') === null) {
            $this->redirect(['index']);
        }
    }

    protected function loadModel($id)
    {
        $model = Book::model()->with('authors')->findByPk((int) $id);

        if ($model === null) {
            throw new CHttpException(404, 'Book not found.');
        }

        return $model;
    }

    protected function getBookService(): BookService
    {
        return $this->getService('bookService', static function () {
            return new BookService();
        });
    }
}
