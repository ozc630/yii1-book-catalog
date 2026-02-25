<?php

class SubscriptionController extends Controller
{
    public function filters()
    {
        return ['accessControl'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['create'], 'users' => ['*']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionCreate($author_id)
    {
        $author = Author::model()->findByPk((int) $author_id);

        if ($author === null) {
            throw new CHttpException(404, 'Author not found.');
        }

        $model = new AuthorSubscription();
        $model->author_id = (int) $author->id;

        $subscriptionPost = Yii::app()->request->getPost('AuthorSubscription');
        if ($subscriptionPost !== null) {
            $model->attributes = $subscriptionPost;
            $model->author_id = (int) $author->id;

            if ($model->saveIdempotent()) {
                Yii::app()->user->setFlash('success', 'Subscription created. You will receive SMS notifications.');
                $this->redirect(['author/view', 'id' => $author->id]);
                return;
            }

            if ($model->hasDuplicateConstraintViolation()) {
                Yii::app()->user->setFlash('info', 'This phone is already subscribed to this author.');
                $this->redirect(['author/view', 'id' => $author->id]);
                return;
            }
        }

        $this->render('create', [
            'model' => $model,
            'author' => $author,
        ]);
    }
}
