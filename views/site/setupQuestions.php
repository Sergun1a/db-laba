<?php
/* Ссылка на виджет мультиселекта
https://github.com/2amigos/yii2-multi-select-widget
 */
/* @var $this yii\web\View */
/* @var $form */

/* @var $model */

use dosamigos\multiselect\MultiSelect;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title                   = 'Подготовка';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <p>Пожалуйста заполните следующие поля:</p>
    <?php $form = ActiveForm::begin([
        'id' => 'question-form',
    ]); ?>
    <?= $form->field($model, 'themes')->widget(MultiSelect::className(), [
        "options"       => ['multiple' => "multiple"],
        'data'          => \app\models\Question::themesList(),
        "clientOptions" =>
            [
                "includeSelectAllOption" => true,
                'allSelectedText'        => "Выбрать все",
                'selectAllText'          => "Выбрать все",
                'nSelectedText'          => 'Выбрано',
                'nonSelectedText'        => 'Выберите тему',
            ],
    ])->label("Выберите тему/темы") ?>
    <?= $form->field($model, 'include_hard')->checkbox()->label("Включать сложные задачи/вопросы") ?>
    <?= Html::submitButton('Готово', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>

    <?php ActiveForm::end(); ?>
</div>

