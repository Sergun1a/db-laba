<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220207_100705_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id'       => $this->primaryKey(),
            'login'    => $this->string(),
            'password' => $this->string(),
            'auth_key' => $this->string(),
            'status'   => $this->string()->defaultValue('active'),
        ]);

        $adminUser = new \app\models\User([
            'login'    => 'admin',
            'password' => 'password',
        ]);
        $adminUser->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
