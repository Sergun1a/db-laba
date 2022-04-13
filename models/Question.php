<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property string $type
 * @property boolean $is_hard
 **/
class Question extends ActiveRecord
{
    const TYPE_THEORY = 'theory';
    const TYPE_PRACTICE = 'practice';
    const TEST_TYPE_KOLLOK = 'kollok';
    const TEST_TYPE_KR = 'kr';
    const TEST_TYPE_EKZ = 'ekz';
    // константы тестового задания
    // закрытые типы заданий
    const ALTERNATIVE_CHOICE = "alternative";
    const MAPPING = "mapping";
    const MULTIPLE_CHOICE = "multiple";
    const SEQUENCE = "sequence";
    // открытые типы заданий
    const ADDITION = "addition";
    const FREE_FORM = "free";

    public static function closeTestingTypeList()
    {
        return [
            1 => self::ALTERNATIVE_CHOICE,
            2 => self::MAPPING,
            3 => self::MULTIPLE_CHOICE,
            4 => self::SEQUENCE,
        ];
    }

    public static function openTestingTypesList()
    {
        return [
            self::ADDITION,
            self::FREE_FORM,
        ];
    }

    public static function testingTypesList()
    {
        return [
            1 => self::ALTERNATIVE_CHOICE,
            2 => self::MAPPING,
            3 => self::MULTIPLE_CHOICE,
            4 => self::SEQUENCE,
            5 => self::ADDITION,
            6 => self::FREE_FORM,
        ];
    }

    public static function specifyTestingTypesList()
    {
        return [
            self::ALTERNATIVE_CHOICE => self::ALTERNATIVE_CHOICE,
            self::MAPPING => self::MAPPING,
            self::MULTIPLE_CHOICE => self::MULTIPLE_CHOICE,
            self::SEQUENCE => self::SEQUENCE,
            self::ADDITION => self::ADDITION,
            self::FREE_FORM => self::FREE_FORM,
        ];
    }

    public static function humanTestingType($type = null)
    {
        $types = [
            self::ALTERNATIVE_CHOICE => 'альтернативный выбор',
            self::MAPPING => 'установление соответствия',
            self::MULTIPLE_CHOICE => 'множественный выбор',
            self::SEQUENCE => 'установление последовательности',
            self::ADDITION => 'дополнение',
            self::FREE_FORM => 'свободное изложение',
        ];

        if (is_null($type)) {
            return $types;
        }

        if (!empty($types[$type])) {
            return $types[$type];
        }
        return null;
    }

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
            [['id'], 'integer'],
            [['type'], 'string'],
            [['is_hard'], 'boolean'],
        ];
    }

    private static function QuestionsToVariants($allowedTestingTypes, $divider, $questions, $rounded_array = true)
    {
        $counter = 0;
        $variants = [];
        shuffle($questions);
        foreach ($questions as $question) {
            if ((is_array($allowedTestingTypes['theme_' . $question->theme->theme_id]) &&
                    in_array($question->content->testing_type, $allowedTestingTypes['theme_' . $question->theme->theme_id]))
                || $question->content->testing_type == $allowedTestingTypes['theme_' . $question->theme->theme_id]) {
                $variants[$counter / $divider + 1][] = $question;
                $counter++;
            }
        }
        // если число вопросов не кратно divider, то избавляюсь от последнего варианта, т.к в нем недостаток вопросов
        if (!$rounded_array) {
            unset($variants[--$counter / $divider + 1]);
        }
        return $variants;
    }

    private static function unsetElement($array, $element)
    {
        $new_array = [];
        foreach ($array as $key => $value) {
            if ($key != $element) {
                $new_array[] = $value;
            }
        }
        return $new_array;
    }

    /**
     * Return theme linked with question.
     * @return \yii\db\ActiveQuery
     */
    public function getTheme()
    {
        return $this->hasOne(QuestionTheme::className(), ['question_id' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'is_hard' => 'Вопрос со звездочкой',
        ];
    }


    public static function prepareQuestions($themes, $include_hard, $points, $allowedTestingType)
    {
        $variants = [];
        $questions = Question::find()
            ->joinWith('theme')
            ->joinWith('content')
            ->andWhere(['in', 'questions_themes.theme_id', $themes])
            ->andWhere(['is_hard' => 0])
            ->all();
        if ($points == -1) {
            if (sizeof($questions) % 3 == 0) {
                $variants = self::QuestionsToVariants($allowedTestingType, 3, $questions);
            } else {
                if (sizeof($questions) % 2 == 0) {
                    $variants = self::QuestionsToVariants($allowedTestingType, 2, $questions);
                } else {
                    $variants = self::QuestionsToVariants($allowedTestingType, 2, $questions, false);
                }
            }
        } else {
            if (sizeof($questions) % $points == 0) {
                $variants = self::QuestionsToVariants($allowedTestingType, $points, $questions);
            } else {
                $variants = self::QuestionsToVariants($allowedTestingType, $points, $questions, false);
            }
        }
        if ($include_hard) {
            $hard_questions = Question::find()
                ->joinWith('theme')
                ->andWhere(['in', 'questions_themes.theme_id', $themes])
                /*->andWhere(['type' => self::TYPE_PRACTICE])*/
                ->andWhere(['is_hard' => 1])
                ->all();
            $previous_variant = -1;
            foreach ($variants as $variant => $question) {
                if ($previous_variant != $variant) {
                    $element = rand(0, sizeof($hard_questions) - 1);
                    if (!empty($hard_questions[$element])) {
                        $variants[$variant][] = $hard_questions[$element];
                        $hard_questions = self::unsetElement($hard_questions, $element);
                    }
                }
                $previous_variant = $variant;
            }
        }
        return $variants;
    }

    private static function randomQuestionForTheme($theme, $type = self::TYPE_THEORY)
    {
        $questions = Question::find()
            ->joinWith('theme')
            ->andWhere(['questions_themes.theme_id' => $theme])
            ->andWhere(['type' => $type])
            ->all();

        $element = rand(0, sizeof($questions) - 1);
        return $questions[$element];
    }

    /**
     * Relation with QuestionContent class
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(QuestionContent::className(), ['question_id' => 'id']);
    }


    public static function availableTestingTypes($themeId)
    {
        $questions = QuestionContent::find()
            ->joinWith('theme')
            ->andWhere(['questions_themes.theme_id' => $themeId])
            ->select('questions_content.testing_type')
            ->distinct()
            ->all();

        $availableTestingTypes = [];
        foreach ($questions as $question) {
            $availableTestingTypes[$question->testing_type] = self::humanTestingType($question->testing_type);
        }
        return array_unique($availableTestingTypes);

    }
}
