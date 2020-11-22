<?php

namespace app\controllers;

use app\models\Question;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['questions'],
                'rules' => [
                    [
                        'actions' => ['questions'],
                        'allow'   => true,
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionQuestions()
    {
        $type = \Yii::$app->request->get('type');
        if ($type == Question::TEST_TYPE_EKZ) {
            $questions = Question::prepareQuestions($type, [], false, 0);
            return $this->render('variants', [
                'type'     => $type,
                'variants' => $questions,
            ]);
        }
        $model = new DynamicModel(['themes', 'include_hard', 'points']);
        $model->addRule('themes', 'safe')->addRule('include_hard', 'boolean')
            ->addRule('points', 'integer', ['min' => 0,'tooSmall'=>'Число должно быть больше 0', 'message' => 'Значение должно быть целым числом'])
            ->addRule('themes', 'required', ['message' => 'Пожалуйста выберите хотя бы одну тему']);
        if ($model->load(Yii::$app->request->post())) {
            if (!is_null($type)) {
                $questions = Question::prepareQuestions($type, $model->themes, $model->include_hard, $model->points == '' ? -1 : $model->points);
                if (empty($questions)) {
                    $model->addError('points','Мы не смогли составить вариант по заданным критериям. Пожалуйста добавьте ещё тем или уменьшить количество баллов');
                    return $this->render('setupQuestions', [
                        'model' => $model,
                    ]);
                }
                return $this->render('variants', [
                    'type'     => $type,
                    'variants' => $questions,
                ]);
            }
        }
        return $this->render('setupQuestions', [
            'model' => $model,
        ]);
    }
}
