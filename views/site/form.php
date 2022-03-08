<?php
/* @var $this yii\web\View */
/* @var $form */

/* @var $question */
/* @var $questionContent */

/* @var $questionTheme */

use app\models\Question;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = "Создание/Редактирование вопроса";
?>
<?php $form = ActiveForm::begin([
    'id' => 'question-edit-form',
]); ?>
<?= $form->field($question, 'type')->dropDownList([Question::TYPE_PRACTICE => 'практика', Question::TYPE_THEORY => 'теория'], ['options' => [
    Question::TYPE_PRACTICE => ['selected' => true]]])->label("Тип вопроса") ?>
<?= $form->field($questionContent, 'testing_type')
    ->dropDownList(Question::humanTestingType())
    ->label("Тип тестового задания") ?>
<?= $form->field($questionContent, 'content')->textarea()->label("Содержание вопроса") ?>
<?= $form->field($questionContent, 'answer_options1')->textarea(['rows' => 1])->label("Варианты ответа (через запятую)") ?>
<?= $form->field($questionContent, 'answer_options2')->textarea(['rows' => 1])
    ->label("Варианты ответа 2 (с чем устанавливать соответствия) (через запятую)") ?>
<br>
<?= $form->field($questionTheme, 'theme_id')
    ->dropDownList(Question::themesList())
    ->label("Выберите тему вопроса") ?>
<br>
<?= $form->field($question, 'is_hard')->checkbox() ?>



<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
<?php ActiveForm::end(); ?>
<?= Html::a('Вернуться в панель', Url::toRoute('site/admin'), ['class' => 'btn btn-success panel-return-button']); ?>

<script>
    $(document).ready(function () {
        let answer_options1 = $('.field-questioncontent-answer_options1');
        let answer_options2 = $('.field-questioncontent-answer_options2');

        function displayAnswerOptions(type) {
            if (type === '<?=Question::MAPPING?>') {
                answer_options1.show();
                answer_options2.show();
            }
            if (type === '<?=Question::FREE_FORM?>' ||
                type === '<?=Question::ADDITION?>') {
                answer_options1.hide();
                answer_options2.hide();
            }
            if (type === '<?=Question::ALTERNATIVE_CHOICE?>' ||
                type === '<?=Question::MULTIPLE_CHOICE?>' ||
                type === '<?=Question::SEQUENCE?>') {
                answer_options1.show();
                answer_options2.hide();
            }
        }

        displayAnswerOptions($('#questioncontent-testing_type').val());
        $('#questioncontent-testing_type').change(function () {
            displayAnswerOptions($('#questioncontent-testing_type').val());
        });
    });
</script>
