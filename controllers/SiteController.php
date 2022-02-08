<?php

namespace app\controllers;

use app\models\Question;
use app\models\QuestionContent;
use app\models\User;
use Codeception\PHPUnit\Constraint\Page;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
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
                        'actions' => ['questions', 'login'],
                        'allow'   => true,
                        'roles'   => ['?', '@'],
                    ],
                    [
                        'actions' => ['admin', 'update', 'delete'],
                        'allow'   => true,
                        'roles'   => ['@'],
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
     * Question action.
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
            ->addRule('points', 'integer', ['min' => 0, 'tooSmall' => 'Число должно быть больше 0', 'message' => 'Значение должно быть целым числом'])
            ->addRule('themes', 'required', ['message' => 'Пожалуйста выберите хотя бы одну тему']);
        if ($model->load(Yii::$app->request->post())) {
            if (!is_null($type)) {
                $questions = Question::prepareQuestions($type, $model->themes, $model->include_hard, $model->points == '' ? -1 : $model->points);
                if (empty($questions)) {
                    $model->addError('points', 'Мы не смогли составить вариант по заданным критериям. Пожалуйста добавьте ещё тем или уменьшите количество баллов');
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

    public function actionLogin()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne([
                'login'  => $model->login,
                'status' => User::STATUS_ACTIVE,
            ]);
            if (empty($user)) {
                $model->addError('login', 'Пользователь с таким логином не обнаружен');
            } else {
                if (!Yii::$app->security->validatePassword($model->password, $user->password)) {
                    $model->addError('password', 'Неверный пароль');
                } else {
                    Yii::$app->user->login($user);
                    return $this->redirect(Url::toRoute('site/admin'));
                }
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionAdmin()
    {
        $dataProvider = new ActiveDataProvider([
            'query'      => QuestionContent::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('grid', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate()
    {
        $questionId = Yii::$app->request->get('id');
        if (!empty($questionId)) {
            $question = QuestionContent::findOne(['question_id' => $questionId]);
        } else {
            $question = new QuestionContent();
        }
        if ($question->load(Yii::$app->request->post())) {
            $question->save();
        }
        return $this->render('form', [
            'model' => $question,
        ]);
    }

    public function actionDelete()
    {
        $questionId = Yii::$app->request->get('id');
        $question   = QuestionContent::findOne(['question_id' => $questionId]);
        if (!empty($question)) {
            $question->delete();
        }
        return $this->redirect(Url::toRoute('site/admin'));
    }
}
