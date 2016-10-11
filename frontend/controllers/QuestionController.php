<?php
namespace frontend\controllers;

use common\controllers\Controller;

class QuestionController extends Controller
{
    /**
     * actionIndex() 显示首页
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * actionChapter() 章节联系
     * @return string
     */
    public function actionChapter()
    {
        return $this->render('chapter');
    }
}
