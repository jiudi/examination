<?php
namespace frontend\controllers;

use common\models\Answer;
use common\models\Question;
use Yii;
use common\models\Chapter;
use common\models\Special;
use common\controllers\Controller;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;


class QuestionController extends Controller
{
    /**
     * actionIndex() 显示首页
     * @return string
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $sType   = $request->get('type', 'all');       // 类型 chapter and special and null
        $intCid  = (int)$request->get('cid');          // 对应类型子类ID
        $sStyle  = $request->get('style', 'sequence'); // 答题类型 sequence 顺序 and random 随机
        $where = [
            'status' => Question::STATUS_KEY
        ];
        switch ($sType) {
            case 'chapter':
                $where['chapter_id'] = $intCid;
                break;
            case 'special':
                $where['special_id'] = $intCid;
                break;
        }

        $question = Question::findOne($where);
        if ($question) {
            // 查询问题答案
            $answer = Answer::findAll(['qid' => $question->id]);
            return $this->render('index', [
                'question' => $question,
                'answer'   => $answer,
            ]);
        } else {
            throw new HttpException(401, '问题不存在');
        }
    }

    /**
     * actionChapter() 章节练习
     * @return string
     */
    public function actionChapter()
    {
        $chapter = Chapter::find()->orderBy('sort')->asArray()->all();
        $counts  = [];
        if ($chapter) {
            $chapterIds = [];
            foreach ($chapter as $value) $chapterIds[] = (int)$value['id'];
            $counts = Yii::$app->db->createCommand('SELECT COUNT(*) AS `length`, `chapter_id` FROM `ks_question` WHERE `chapter_id` IN ('.implode(',', $chapterIds).') GROUP BY `chapter_id`')->queryAll();
            if ($counts) $counts = ArrayHelper::map($counts, 'chapter_id', 'length');
        }

        return $this->render('chapter', [
            'chapter' => $chapter, // 章节
            'counts'  => $counts,  // 章节对应题目数
        ]);
    }

    /**
     * actionSpecial() 专项练习
     * @return string
     */
    public function actionSpecial()
    {
        $special = Special::find()->asArray()->all();
        $all = $ids = $counts = [];
        if ($special) {
            foreach ($special as $value) {
                $intKid = $value['pid'] == 0 ? $value['id'] : $value['pid'];
                if ($value['pid'] == 0) {
                    if (isset($all[$intKid])) {
                        $all[$intKid] = array_merge($all[$intKid], $value);
                    } else {
                        $all[$intKid] = array_merge($value, ['child' => []]);
                    }
                } else {
                    $ids[] = (int)$value['id'];
                    if (isset($all[$intKid])) {
                        $all[$intKid]['child'][$value['sort'].'-'.$value['id']] = $value;
                    } else {
                        $all[$intKid] = [
                            'child' => [$value['sort'].'-'.$value['id'] => $value],
                        ];
                    }
                }
            }

            // 查询
            $counts = Yii::$app->db->createCommand('SELECT COUNT(*) AS `length`, `special_id` FROM `ks_question` WHERE `special_id` IN ('.implode(',', $ids).') GROUP BY `special_id`')->queryAll();
            if ($counts) $counts = ArrayHelper::map($counts, 'special_id', 'length');
            $files = [];
            foreach ($all as $k => &$v) {
                $files[$k] = $v['sort'];
                ksort($v['child']);
            }

            array_multisort($files, SORT_ASC, $all);
        }

        return $this->render('special', [
            'special' => $all,
            'counts'  => $counts,
        ]);
    }
}
