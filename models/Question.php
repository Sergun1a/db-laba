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
}
