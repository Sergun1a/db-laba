<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

$this->title = 'Электронная форма учебника-задачника';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать!</h1>
        <p class="lead">Электронная форма учебника-задачника</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Коллоквиум</h2>
                <p>Список задач для проведения коллоквиума по одной или нескольким темам, или по всему курсу</p>
                <p><a class="btn btn-default" href="<?php echo Url::toRoute(['site/questions', 'type' => 'kollok']); ?>">Создать &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Контрольная работа</h2>
                <p>Список задач для проведения контрольной по одной или нескольким темам, или по всему курсу</p>
                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Создать &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Экзамен</h2>
                <p>Билеты к экзамену, состоящие из двух теоретических вопросов и одной задачи</p>
                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Создать &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
