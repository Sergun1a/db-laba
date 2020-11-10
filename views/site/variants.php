<?php
/* @var $variants */
/* @var $type */

$this->title                   = 'Варианты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="breadcrumb">
    <a href="#question">Варианты с текстами задач и вопросами</a> / <a href="#text">Варианты с номерами задач и
        вопросами</a>
</div>


<div class="container">
    <span id="question"><h1>Варианты с текстами задач и вопросами</h1></span>
    <hr>
    <?php foreach ($variants as $variant => $questions) { ?>
        <div class="row">
            <h2><?= $variant ?> вариант</h2>
            <ul>
                <?php foreach ($questions as $question) { ?>
                    <li>
                        <div class="col-md col-12">
                            <span><?= $question->question ?></span>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <hr>
    <?php } ?>
    <span id="text"><h1>Варианты с номерами задач и вопросами</h1></span>
    <hr>
    <?php foreach ($variants as $variant => $questions) { ?>
        <div class="row">
            <h2><?= $variant ?> вариант</h2>
            <ul>
                <?php foreach ($questions as $question) { ?>
                    <li>
                        <div class="col-md col-12">
                            <p class="pull-left">Тема "<?= \app\models\Question::themesList()[$question->theme_id]; ?>
                                ", вопрос номер
                                <?= $question->question_number ?></p>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <hr>
    <?php } ?>
</div>

