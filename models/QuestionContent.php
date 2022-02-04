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
 * @property json   $answer_options
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
            [['answer_options'], 'validateJSON'],
        ];
    }

    public function validateJSON($attribute, $params)
    {
        if (in_array($this->testing_type, Question::closeTestingTypeList())) {
            try {
                Json::decode($this->$attribute);
            } catch (\yii\base\InvalidArgumentException $ex) {
                $this->addError($attribute, "Недопустимый формат вариантов ответа");
            }
        } else {
            $this->$attribute = Json::encode([]);
        }
    }
}