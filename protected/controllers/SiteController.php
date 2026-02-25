<?php

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ],
            'page' => [
                'class' => 'CViewAction',
            ],
        ];
    }

    public function filters()
    {
        return ['accessControl', 'postOnly + logout'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['index', 'login', 'error'], 'users' => ['*']],
            ['allow', 'actions' => ['logout'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionError()
    {
        if (($error = Yii::app()->errorHandler->error) !== null) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', ['error' => $error]);
            }
        }
    }

    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect(['book/index']);
            return;
        }

        $model = new LoginForm();

        $loginPost = Yii::app()->request->getPost('LoginForm');
        if ($loginPost !== null) {
            $model->attributes = $loginPost;
            if ($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->returnUrl);
                return;
            }
        }

        $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(['site/login']);
    }
}
