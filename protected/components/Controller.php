<?php

class Controller extends CController
{
    public $layout = '//layouts/main';
    public $menu = [];
    public $breadcrumbs = [];
    private $services = [];

    protected function requirePostRequest()
    {
        if (!Yii::app()->request->isPostRequest) {
            throw new CHttpException(405, 'Method Not Allowed');
        }
    }

    public function setService(string $id, $service): void
    {
        $this->services[$id] = $service;
    }

    protected function getService(string $id, callable $factory)
    {
        if (!array_key_exists($id, $this->services)) {
            $this->services[$id] = $factory();
        }

        return $this->services[$id];
    }
}
