<?php
/**
 * file: QuestionController.php
 * desc: 题库信息 执行操作控制器
 * date: 2016-10-11 16:49:26
 */

namespace backend\controllers;

use common\models\Question;

class QuestionController extends Controller
{
    /**
     * where() 查询处理
     * @param  array $params
     * @return array 返回数组
     */
    public function where($params)
    {
        return [
             
        ];
    }

    /**
     * getModel() 返回model
     * @return Question
     */
    public function getModel()
    {
        return new Question();
    }
}
