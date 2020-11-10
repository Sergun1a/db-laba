<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property string  $question
 * @property int     $theme_id
 * @property string  $type
 * @property boolean $is_hard
 **/
class Question extends ActiveRecord
{
    const TYPE_THEORY      = 'theory';
    const TYPE_PRACTICE    = 'practice';
    const TEST_TYPE_KOLLOK = 'kollok';
    const TEST_TYPE_KR     = 'kr';
    const TEST_TYPE_EKZ    = 'ekz';

    public static function themesList()
    {
        return [
            1 => 'Что такое базы данных',
            2 => 'Реляционная модель данных',
            3 => 'Язык SQL',
            4 => 'Проектирование на основе принципов нормализации',
            5 => 'Логическое моделирование. Модель «сущность-связь»',
            6 => 'Транзакции',
            7 => 'Технологии клиент-сервер',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'theme_id'], 'integer'],
            [['question', 'type'], 'string'],
            [['is_hard'], 'boolean'],
        ];
    }

    private static function QuestionsToVariants($divider, $questions, $rounded_array = true)
    {
        $counter  = 0;
        $variants = [];
        shuffle($questions);
        foreach ($questions as $question) {
            $variants[$counter / $divider + 1][] = $question;
            $counter++;
        }
        if (!$rounded_array) {
            unset($variants[--$counter / $divider + 1]);
        }
        return $variants;
    }

    public static function prepareQuestions($type, $themes, $include_hard)
    {
        $variants = [];
        if ($type == self::TEST_TYPE_KOLLOK) {
            $questions = Question::find()
                ->andWhere(['in', 'theme_id', $themes])
                ->andWhere(['type' => self::TYPE_THEORY])
                ->all();
            if (sizeof($questions) % 5 == 0) {
                $variants = self::QuestionsToVariants(5, $questions);
            } else {
                if (sizeof($questions) % 4 == 0) {
                    $variants = self::QuestionsToVariants(4, $questions);
                } else {
                    if (sizeof($questions) % 3 == 0) {
                        $variants = self::QuestionsToVariants(3, $questions);
                    } else {
                        $variants = self::QuestionsToVariants(3, $questions, false);
                    }
                }
            }
            return $variants;
        }
        if ($type == self::TEST_TYPE_KR) {

        }
        if ($type == self::TEST_TYPE_EKZ) {

        }
    }
}
