<?php

class AuthorController extends Controller
{
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
        $criteria = new CDbCriteria();
        $criteria->select = 't.id, t.name, COUNT(ba.book_id) AS books_count';
        $criteria->join = 'LEFT JOIN book_author ba ON ba.author_id = t.id';
        $criteria->group = 't.id, t.name';
        $criteria->order = 't.name ASC';

        $authors = Author::model()->findAll($criteria);
        $this->render('index', ['authors' => $authors]);
    }

    public function actionView($id)
    {
        $author = $this->loadModel($id);
        $this->render('view', ['author' => $author]);
    }

    public function actionCreate()
    {
        $model = new Author();

        $authorPost = Yii::app()->request->getPost('Author');
        if ($authorPost !== null) {
            $model->attributes = $authorPost;
            if ($model->save()) {
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        $authorPost = Yii::app()->request->getPost('Author');
        if ($authorPost !== null) {
            $model->attributes = $authorPost;
            if ($model->save()) {
                $this->redirect(['view', 'id' => $model->id]);
                return;
            }
        }

        $this->render('update', ['model' => $model]);
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
        $model = Author::model()->with('books')->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'Author not found.');
        }

        return $model;
    }
}
