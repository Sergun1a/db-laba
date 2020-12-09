<?php

use yii\db\Migration;

/**
 * Class m201209_123423_fill_questions_themes_table
 */
class m201209_123423_fill_questions_themes_table extends Migration
{
    public function up()
    {
        $questions = \app\models\Question::find()->all();
        $this->addForeignKey('QuestionIDFK', 'questions_themes', 'question_id', 'questions', 'id');
        foreach ($questions as $question) {
            (new \app\models\QuestionTheme([
                'question_id'     => $question->id,
                'theme_id'        => $question->theme_id,
                'question_number' => $question->question_number,
            ]))->save(false);
        }
        $this->dropColumn('questions', 'theme_id');
        $this->dropColumn('questions', 'question_number');
    }

    public function down()
    {
        $this->dropForeignKey('QuestionIDFK', 'questions_themes');
        Yii::$app->db->createCommand()->truncateTable('questions_themes')->execute();
        return true;
    }
}
