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

            </div>
            <div class="col-lg-4">
                <h2>Тесты</h2>
                <p>Создать тестовое задание из указанных тем</p>
                <p><a class="btn btn-default"
                      href="<?php echo Url::toRoute(['site/questions', 'type' => 'test']); ?>">Создать
                        &raquo;</a></p>
            </div>
            <div class="col-lg-4">

            </div>
        </div>

    </div>
</div>
