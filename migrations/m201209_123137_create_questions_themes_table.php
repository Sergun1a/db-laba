<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%questiona_themes}}`.
 */
class m201209_123137_create_questions_themes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%questions_themes}}', [
            'question_id'     => $this->primaryKey(11),
            'theme_id'        => $this->smallInteger(1),
            'question_number' => $this->smallInteger(2),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%questions_themes}}');
    }
}
