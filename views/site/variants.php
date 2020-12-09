<?php
/* @var $variants */
/* @var $type */

$this->title = 'Варианты';
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
            <ol>
                <?php foreach ($questions as $question) { ?>
                    <li>
                        <div class="col-md col-12">
                            <span><?= $question->is_hard && $type != \app\models\Question::TEST_TYPE_EKZ ? '<sup>&#9913;</sup>' : ''; ?> <?= $question->question ?></span>
                        </div>
                    </li>
                <?php } ?>
            </ol>
        </div>
        <hr>
    <?php } ?>
    <span id="text"><h1>Варианты с номерами задач и вопросами</h1></span>
    <hr>
    <?php foreach ($variants as $variant => $questions) { ?>
        <div class="row">
            <h2><?= $variant ?> вариант</h2>
            <ol>
                <?php foreach ($questions as $question) { ?>
                    <li>
                        <div class="col-md col-12">
                            <p class="pull-left">Тема "<?= \app\models\Question::themesList()[$question->theme->theme_id]; ?>
                                ", <?= $question->type == \app\models\Question::TYPE_THEORY ? "контрольный вопрос" :
                                    "практический вопрос"; ?>
                                <?php if (!$question->is_hard) { ?>
                                номер
                                <?= $question->theme->question_number ?></p>
                            <?php } else { ?>
                                со звездочкой
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>
            </ol>
        </div>
        <hr>
    <?php } ?>
</div>

