<?php

namespace common\models;

/**
 * This is the model class for table "{{%question}}".
 *
 * @property integer $id
 * @property string $quest_title
 * @property string $question_content
 * @property integer $answer_type
 * @property integer $status
 * @property integer $answer_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $subject_id
 * @property integer $chapter_id
 * @property integer $special_id
 * @property integer $error_number
 */
class Question extends TimestampModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quest_title', 'answer_id'], 'required'],
            [['quest_title', 'question_content'], 'string'],
            [['answer_type', 'status', 'answer_id', 'created_at', 'updated_at', 'subject_id', 'chapter_id', 'special_id', 'error_number'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '题目ID',
            'quest_title' => '题目问题',
            'question_content' => '题目说明',
            'answer_type' => '答案类型(1 单选 2 多选 3 判断)',
            'status' => '状态(1 启用 0 停用)',
            'answer_id' => '正确答案ID',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'subject_id' => '所属科目ID',
            'chapter_id' => '所属章节',
            'special_id' => '专项ID',
            'error_number' => '错误人数',
        ];
    }
}
