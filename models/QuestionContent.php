<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "questions_content".
 *
 * @property int    $id
 * @property int    $question_id
 * @property string $content
 * @property string $testing_type
 * @property json   $answer_options1
 * @property json   $answer_options2
 */
class QuestionContent extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id'], 'integer'],
            [['content'], 'string'],
            [['testing_type'], 'in', 'range' => Question::testingTypesList(), 'message' => 'Недопустимый тип тестового задания'],
            [['answer_options1', 'answer_options2'], 'validateAnswers'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content' => 'Содержание вопроса',
        ];
    }

    public function validateAnswers($attribute, $params)
    {
        if (in_array($this->testing_type, Question::closeTestingTypeList())) {
            try {
                Json::encode($this->$attribute);
            } catch (\yii\base\InvalidArgumentException $ex) {
                $this->addError($attribute, "Недопустимый формат вариантов ответа");
            }
        }
    }
}