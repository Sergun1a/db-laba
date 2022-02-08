<?php
/* @var $this yii\web\View */
/* @var $form */

/* @var $dataProvider */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Панель редактирования'
?>
<?= Html::a('Создать новое задание', Url::toRoute('site/update'), ['class' => 'btn btn-success question-create-button']); ?>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'content',
        [
            'class' => 'yii\grid\DataColumn',
            'label' => 'Тип тестового задания',
            'value' => function ($data) {
                return \app\models\Question::humanTestingType($data->testing_type);
            },
        ],
        [
            'class'   => 'yii\grid\ActionColumn',
            'buttons' => [
                'view' => function ($url, $model) {
                },
            ],
        ],
    ],
]);
?>
