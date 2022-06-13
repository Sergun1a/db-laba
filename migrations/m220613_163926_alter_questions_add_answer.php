<?php

use yii\db\Migration;

/**
 * Class m220613_163926_alter_questions_add_answer
 */
class m220613_163926_alter_questions_add_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('questions_content', 'answer', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('questions_content', 'answer');
    }
}
