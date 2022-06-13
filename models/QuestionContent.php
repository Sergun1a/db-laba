<?php

namespace app\models;

use Codeception\PHPUnit\Constraint\Page;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "questions_content".
 *
 * @property int $id
 * @property int $question_id
 * @property string $content
 * @property string $testing_type
 * @property json $answer_options1
 * @property json $answer_options2
 * @property string $answer
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
            [['answer'], 'existingAnswerOptions']
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

    public function getTheme()
    {
        return $this->hasOne(QuestionTheme::className(), ['question_id' => 'question_id']);
    }

    public function existingAnswerOptions($attribute, $params)
    {
        // задание с открытым ответом, не требует ответа в мудл
        if ($this->testing_type == Question::FREE_FORM) {
            return;
        }
        if ($this->testing_type == Question::ALTERNATIVE_CHOICE) {
            if (!in_array($this->answer, $this->answersOptionsToArray())) {
                $this->addError($attribute, 'Указанный ответ отсутствует в вариантах ответа');
            }
        }
        if ($this->testing_type == Question::MAPPING) {
            $answers = $this->answerToArray();
            $answerOptions = $this->answersOptionsToArray();
            if (sizeof($answers) > sizeof($answerOptions[0]) * 2) {
                $this->addError($attribute, 'Количество ответов превышает их возможное количество.');
                return;
            }
            foreach ($answers as $key => $value) {
                // первый столбец
                if ($key % 2 == 0) {
                    if (!in_array($value, $answerOptions[0])) {
                        $this->addError($attribute, $value . ' не найден в первом ряду вариантов ответа. Проверьте нечетные элементы в ответе.');
                        return;
                    }
                }
                // второй столбец
                if ($key % 2 == 1) {
                    if (!in_array($value, $answerOptions[1])) {
                        $this->addError($attribute, $value . ' не найден во втором ряду вариантов ответа. Проверьте четные элементы в ответе.');
                        return;
                    }
                }
            }
        }
        if ($this->testing_type == Question::MULTIPLE_CHOICE) {
            $answers = $this->answerToArray();
            $answerOptions = $this->answersOptionsToArray();
            foreach ($answers as $answer) {
                if (!in_array($answer, $answerOptions)) {
                    $this->addError($attribute, 'Ответ "' . $answer . '" отсутствует в вариантах ответа');
                    return;
                }
            }
        }
        if ($this->testing_type == Question::SEQUENCE) {
            $answers = $this->answerToArray();
            $answerOptions = $this->answersOptionsToArray();
            if (sizeof($answers) != sizeof($answerOptions)) {
                $this->addError($attribute, 'Количество ответов должно совпадать с количеством вариантов ответа');
                return;
            }
            foreach ($answers as $answer) {
                if (!in_array($answer, $answerOptions)) {
                    $this->addError($attribute, 'Ответ "' . $answer . '" отсутствует в вариантах ответа');
                    return;
                }
            }
        }

    }

    private function answerToArray()
    {
        return $this->standartify(explode(';', $this->answer));
    }


    private function answersOptionsToArray()
    {
        $optionsArray = [];
        if ($this->testing_type == Question::FREE_FORM) {
            return [];
        }
        if ($this->testing_type != Question::MAPPING) {
            return $this->standartify(explode(';', (string)$this->answer_options1));
        }
        $optionsArray[0] = $this->standartify(explode(';', (string)$this->answer_options1));
        $optionsArray[1] = $this->standartify(explode(';', (string)$this->answer_options2));
        return $optionsArray;
    }

    /**
     * Updates string to be in a standard type
     * @param $array
     * @return array
     */
    private function standartify($array)
    {
        $purifiedArray = [];
        foreach ($array as $key => $elem) {
            if (empty($elem)) {
                continue;
            }
            $purifiedArray[$key] = strtolower(trim($elem));
        }
        return $purifiedArray;
    }
}