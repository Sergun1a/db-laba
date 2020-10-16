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
    const TYPE_THEORY   = 'theory';
    const TYPE_PRACTICE = 'practice';

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


    public static function prepareQuestions($type, $themes, $include_hard) {

    }
}
