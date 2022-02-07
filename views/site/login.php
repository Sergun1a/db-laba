<?php
/* @var $this yii\web\View */
/* @var $form */

/* @var $model */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход в админ панель';
?>
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
]); ?>
<?= $form->field($model, 'login')->label("Логин") ?>
<?= $form->field($model, 'password')->label("Пароль") ?>
<?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
<?php ActiveForm::end(); ?>
