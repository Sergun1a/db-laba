<?php

use yii\db\Migration;

/**
 * Class m201016_160425_create_table_questions
 */
class m201016_160425_create_table_questions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('questions', [
            'id'              => $this->primaryKey(),
            'question'        => $this->text(),
            'theme_id'        => $this->smallInteger(1),
            'question_number' => $this->smallInteger(2),
            'type'            => $this->text(),
            'is_hard'         => $this->boolean()->defaultValue(false),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('questions');
    }
}
