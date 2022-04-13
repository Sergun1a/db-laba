<?php
/* @var $variants */
/* @var $type */

$this->title = 'Варианты';
?>

<div class="breadcrumb">
    <a href="#question">Варианты с текстами задач и вопросами</a>
</div>


<div class="container">
    <span id="question"><h1>Варианты с текстами задач и вопросами</h1></span>
    <hr>
    <?php foreach ($variants

                   as $variant => $questions) { ?>
        <div class="row">
            <h2><?= $variant ?> вариант</h2>
            <ol>
                <?php foreach ($questions

                               as $question) { ?>
                    <li>
                        <div class="col-md col-12">
                            <span><?= $question->is_hard ? '<sup>&#9913;</sup>' : ''; ?> <?= $question->content->content ?></span>
                            <?php if (in_array($question->content->testing_type, \app\models\Question::closeTestingTypeList())) { ?>
                                <?php foreach (explode(';', $question->content->answer_options1) as $key => $option) { ?>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6"><?= $key + 1 . ') ' . trim($option) ?></div>
                                        <?php if ($question->content->testing_type == \app\models\Question::MAPPING) {
                                            $options2 = explode(',', $question->content->answer_options2);
                                            if (!empty($options2[$key])) { ?>
                                                <div class="col-md-6 col-lg-6"><?= $key + 1 . ') ' . trim($options2[$key]) ?></div>
                                            <?php }
                                        } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($question->content->testing_type == \app\models\Question::MAPPING &&
                                    explode(',', $question->content->answer_options1) < explode(',', $question->content->answer_options2)) {
                                    $options2 = explode(',', $question->content->answer_options2);
                                    $options1 = explode(',', $question->content->answer_options1);
                                    for ($i = sizeof($options1); $i < sizeof($options2); $i++) { ?>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6"></div>
                                            <?php if ($question->content->testing_type == \app\models\Question::MAPPING) {
                                                if (!empty($options2[$i])) { ?>
                                                    <div class="col-md-6 col-lg-6"><?= $i+1 . ') ' . trim($options2[$i]) ?></div>
                                                <?php }
                                            } ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>
            </ol>
        </div>
        <hr>
    <?php } ?>
</div>

