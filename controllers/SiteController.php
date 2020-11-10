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
        $model = new DynamicModel(['themes', 'include_hard']);
        $model->addRule('themes', 'safe')->addRule('include_hard', 'boolean')
            ->addRule('themes', 'required', ['message' => 'Пожалуйста выберите хотя бы одну тему']);
        if ($model->load(Yii::$app->request->post())) {
            $type = \Yii::$app->request->get('type');
            if (!is_null($type)) {
                // do somenthing with model
                $questions = Question::prepareQuestions($type, $model->themes, $model->include_hard);
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
