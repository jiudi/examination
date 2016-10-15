<?php
namespace frontend\controllers;

use common\models\UserCollect;
use Yii;
use common\models\Answer;
use common\models\Question;
use common\models\Chapter;
use common\models\Special;
use common\controllers\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\HttpException;


class QuestionController extends Controller
{
    /**
     * actionIndex() 显示首页
     * @return string
     */
    public function actionIndex()
    {
        // 接收参数
        $request = Yii::$app->request;
        $sType   = $request->get('type', 'all');       // 类型 chapter and special and null
        $intCid  = (int)$request->get('cid');          // 对应类型子类ID
        $sStyle  = $request->get('style', 'sequence'); // 答题类型 sequence 顺序 and random 随机
        $where = [
            'status' => Question::STATUS_KEY,
            'subject_id' => 1
        ];

        // 全部题目
        $allTotal = Question::find()->where($where)->count(); // 全部题库

        // 根据类型查询数据
        switch ($sType) {
            case 'chapter':
                $where['chapter_id'] = $intCid;
                break;
            case 'special':
                $where['special_id'] = $intCid;
                break;
        }

        // 开始查询
        $total    = Question::find()->where($where)->count();
        $all = Question::find()->select('id')->where($where)->indexBy('id')->all();
        $ids = array_keys($all);
        $question = Question::findOne($where); // 查询一条数据

        if ($question) {
            // 查询问题答案
            $answer = Answer::findAll(['qid' => $question->id]);
            return $this->render('index', [
                'allTotal' => (int)$allTotal,
                'total'    => (int)$total,
                'hasCollect'  => UserCollect::hasCollect($question->id),
                'allIds'   => Json::encode($ids),
                'question' => $question,
                'answer'   => $answer,
                'style'    => $sStyle,
                'type'     => $sType,
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

    /**
     * actionRecord() 记录用户做题信息
     * @return mixed|string
     */
    public function actionRecord()
    {
        $request = Yii::$app->request;
        $intQid  = $request->post('qid');
        $strType = $request->post('sType', 'no');
        if ($intQid) {
            $question = Question::findOne($intQid);
            $this->arrJson['errCode'] = 220;
            if ($question) {
                $cookie = $request->cookies;

                $values = $cookie->get($strType);
                if ($values == null) {
                    $values = [$intQid];
                } else {
                    $values = Json::decode($values, true);
                    array_push($values, $intQid);
                    $values = array_unique($values);
                }

                // 添加COOKIE
                Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => $strType,
                    'value' => Json::encode($values),
                    'expire' => time() + 86400,
                ]));

                // 修改记录信息
                if ($strType == 'no') $question->error_number ++;
                $question->do_number ++;
                $question->save();

                $this->handleJson($values);

            }
        }

        return $this->returnJson();
    }

    /**
     * actionGetQuestion() 获取问题和答案信息
     * @return mixed|string
     */
    public function actionGetQuestion()
    {
        $id = (int)Yii::$app->request->post('qid');
        if ($id) {
            $this->arrJson['errCode'] = 220;
            $question = Question::findOne($id);
            if ($question) {
                $answers = Answer::findAll(['qid' => $question->id]);
                $this->handleJson([
                    'hasCollect' => UserCollect::hasCollect($question->id),
                    'question' => $question,
                    'answers' => $answers
                ]);
            }
        }

        return $this->returnJson();
    }

    /**
     * actionInstall() 盗取别人的数据
     * @return mixed|string
     */
    public function actionInstall()
    {
        $request = Yii::$app->request;
        $title = trim($request->get('title'));
        $items = $request->get('items');
        $answer = $request->get('answer');
        $content = trim($request->get('content'));
        $answer_type = (int)$request->get('answer_type');
        if ($title && $items) {
            $model = new Question();
            $model->answer_type = $answer_type;
            $model->question_title = $title;
            $model->question_content = $content;
            $model->status = 1;
            $model->subject_id = 1;
            $model->chapter_id = 1;
            if ($model->save()) {
                foreach ($items as $key => $val) {
                    $tmp = new Answer();
                    $tmp->name = $val;
                    $tmp->qid  = $model->id;
                    if ($tmp->save() && $key == $answer) {
                        $model->answer_id = $tmp->id;
                        $model->save(false);
                    }
                }
            }
        }

        return $this->returnJson();
    }

    public function actionDown()
    {
        $imgPath = 'http://file.open.jiakaobaodian.com/tiku/res/802800.jpg';
        $strPath = str_replace('http://file.open.jiakaobaodian.com/', './upload/', $imgPath);
        $dirPath = dirname($strPath);
        if (! file_exists($dirPath)) mkdir($dirPath, 0777, true);
        file_put_contents($strPath, file_get_contents($imgPath));

    }
}
