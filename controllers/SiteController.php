<?php

namespace app\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
        $model->addRule('themes', 'safe')->addRule('include_hard', 'boolean');
        if($model->load(Yii::$app->request->post())){
            // do somenthing with model
            return $this->render('index');
        }
        return $this->render('setupQuestions', [
            'model' => $model,
        ]);
    }
}
