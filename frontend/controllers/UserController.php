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
    public function actionCollect()
    {

    }

    /**
     * actionCreateCollect() 添加用户收藏信息
     * @return array 返回json字符串
     */
    public function actionCreateCollect()
    {
        $intQid = (int)Yii::$app->request->post('qid');
        $strType = Yii::$app->request->post('type');
        if ($intQid && $strType && in_array($strType, ['create', 'remove'])) {
            // 查询对象
            $model = UserCollect::findOne(['user_id' => Yii::$app->user->id]);
            if ( ! $model) {
                $model = new UserCollect();
                $model->user_id = Yii::$app->user->id;
                $model->qids = [];
            }

            $array = $model->qids;
            $isTrue = false;
            if ($strType == 'create') {
                // 获取之前的收藏信息
                $this->arrJson['errCode'] = 222;
                if (! in_array($intQid, $model->qids) || empty($model->qids)) {
                    array_push($array, $intQid);
                    $array = array_unique($array);
                    $isTrue = true;
                }
            } else {
                $this->arrJson['errCode'] = 224;
                if (in_array($intQid, $model->qids)) {
                    $intKey = array_search($intQid, $array);
                    if ($intKey !== false) unset($array[$intKey]);
                    $isTrue = true;
                }
            }

            if ($isTrue) {
                $model->qids = Json::encode($array);
                if ($model->save()) $this->handleJson($model);
            }
        }

        return $this->returnJson();
    }
}