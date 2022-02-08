<?php

use yii\db\Migration;

/**
 * Class m220204_145059_add_questions_content_table
 */
class m220204_145059_add_questions_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('questions_content', [
            'question_id'    => $this->primaryKey(),
            'testing_type'   => $this->string()->defaultValue(\app\models\Question::FREE_FORM),
            'content'        => $this->text(),
            'answer_options1' => $this->text(),
            'answer_options2' => $this->text(),
        ]);
        $questions = \app\models\Question::find()->all();

        foreach ($questions as $question) {
            $content = new \app\models\QuestionContent([
                'question_id' => $question->id,
                'content'     => $question->question,
            ]);
            $content->save();
        }
        $this->dropColumn('questions', 'question');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('questions', 'question', $this->text());
        $questions = \app\models\QuestionContent::find()
            ->andWhere(['testing_type' => \app\models\Question::FREE_FORM])
            ->all();

        foreach ($questions as $question) {
            $fullQuestion = \app\models\Question::findOne(['id' => $question->question_id]);
            if (!empty($fullQuestion)) {
                $fullQuestion->question = $question->content;
                $fullQuestion->save(false);
            }
        }
        $this->dropTable('questions_content');
    }
}
