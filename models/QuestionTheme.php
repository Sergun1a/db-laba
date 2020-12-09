<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "questions_themes".
 *
 * @property integer $question_id
 * @property integer $theme_id
 * @property integer $question_number
 **/
class QuestionTheme extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions_themes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'theme_id', 'question_number'], 'integer'],
        ];
    }
}
