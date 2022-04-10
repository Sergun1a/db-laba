<?php
/* @var $this yii\web\View */
/* @var $form */

/* @var $model */
/* @var $setupModel */


use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Уточнение разрешенных типов тестовых заданий';
?>
<div class="site-login">
    <p>Пожалуйста укажите разрешенные типы тестовых заданий для каждой из выбранных тем:</p>
    <?php $form = ActiveForm::begin([
        'id' => 'specify-testing-type-form',
    ]); ?>
    <?php foreach ($setupModel->themes as $theme) { ?>
        <div>
            <p><b><?= \app\models\Question::themesList()[$theme] ?></b></p>
            <?= $form->field($model, 'theme_'.$theme)->inline(true)->checkboxList(\app\models\Question::humanTestingType(), ['checked' => 'checked'])->label(false)?>
        </div>
    <?php } ?>
    <?= Html::submitButton('Готово', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
    <?php ActiveForm::end(); ?>
</div>