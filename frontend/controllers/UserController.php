<?php
/**
 * Created by PhpStorm.
 * Date: 2016/10/14
 * Time: 12:00
 */

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Question;
use common\models\Subject;
use common\models\UserCollect;
use common\models\Answer;

/**
 * Class UserController
 * @package frontend\controllers
 */
class UserController extends \common\controllers\UserController
{
    public function actionCollect()
    {
        // 查询科目
        $subject = Subject::findOne(Yii::$app->request->get('subject', 1));
        if ($subject) {
            $collect = UserCollect::findOne([
                'user_id' => Yii::$app->user->id,
                'subject_id' => $subject->id,
            ]);

            // 有收藏
            if ($collect && $collect->qids) {
                // 全部题目
                $allTotal = Question::find()->where([
                    'status' => Question::STATUS_KEY,
                    'subject_id' => $subject->id
                ])->count(); // 全部题库

                Yii::$app->view->params['breadcrumbs'] = [
                    [
                        'label' => $subject->name,
                        'url' => Url::toRoute(['/', 'subject' => $subject->id]),
                    ],
                    [
                        'label' => '我的收藏',
                        'url' => Url::toRoute(['user/collect', 'subject' => $subject->id])
                    ],
                    '顺序练习',
                ];

                // 开始查询
                $question = Question::findOne($collect->qids[0]); // 查询一条数据
                if ($question) {
                    // 查询问题答案
                    $answer = Answer::findAll(['qid' => $question->id]);
                    return $this->render('/question/index', [
                        'allTotal' => (int)$allTotal,
                        'total' => count($collect->qids),
                        'hasCollect' => UserCollect::hasCollect($question->id),
                        'allIds' => Json::encode($collect->qids),
                        'question' => $question,
                        'answer' => $answer,
                        'style' => 'sequence',
                    ]);
                }
            }
        }

        // 没有数据直接返回
        return $this->redirect(['/', 'subject' => 1]);
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