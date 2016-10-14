<?php
/**
 * Created by PhpStorm.
 * Date: 2016/10/14
 * Time: 12:00
 */

namespace frontend\controllers;

use Yii;
use common\models\UserCollect;
use yii\helpers\Json;

/**
 * Class UserController
 * @package frontend\controllers
 */
class UserController extends \common\controllers\UserController
{
    /**
     * actionCreateCollect() 添加用户收藏信息
     * @return array 返回json字符串
     */
    public function actionCreateCollect()
    {
        $intQid = (int)Yii::$app->request->post('qid');
        if ($intQid) {
            // 查询对象
            $model = UserCollect::findOne(['user_id' => Yii::$app->user->id]);
            if ( ! $model) {
                $model = new UserCollect();
                $model->user_id = Yii::$app->user->id;
                $model->qids = [];
            }

            // 获取之前的收藏信息
            $this->arrJson['errCode'] = 222;
            if ( ! $model->qids || !in_array($intQid, $model->qids)) {
                $array = $model->qids;
                array_push($array, $intQid);
                $model->qids = Json::encode($array);
                if ($model->save())
                    $this->handleJson($model);
                else
                    var_dump($model->getErrorString());
            }
        }

        return $this->returnJson();
    }
}