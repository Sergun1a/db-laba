<?php

namespace app\controllers;

use app\models\Question;
use app\models\QuestionContent;
use app\models\QuestionTheme;
use app\models\User;
use Codeception\PHPUnit\Constraint\Page;
use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\RangeNotSatisfiableHttpException;

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
                'only' => ['questions'],
                'rules' => [
                    [
                        'actions' => ['questions', 'login', 'testingType', 'moodleXml'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['admin', 'update', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
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
        try {
            $cache = Yii::$app->cache;
            $type = \Yii::$app->request->get('type');
            $model = new DynamicModel(['themes', 'include_hard', 'points']);
            $model->addRule('themes', 'safe')->addRule('include_hard', 'boolean')
                ->addRule('points', 'integer', ['min' => 0, 'tooSmall' => 'Число должно быть больше 0', 'message' => 'Значение должно быть целым числом'])
                ->addRule('themes', 'required', ['message' => 'Пожалуйста выберите хотя бы одну тему']);
            if ($model->load(Yii::$app->request->post())) {
                if ($cache->get('status') == 'testing_type') {
                    $model->themes = $cache->get('themes');
                    $model->points = $cache->get('points');
                } else {
                    $cache->set('status', 'themes');
                    $cache->set('themes', $model->themes);
                    $cache->set('points', $model->points);
                }

                $specifyModelFields = [];
                $specifyModelValues = [];
                foreach ($model->themes as $theme) {
                    $specifyModelFields[] = 'theme_' . $theme;
                    $specifyModelValues['theme_' . $theme] = Question::specifyTestingTypesList();
                }
                $specifyModel = new DynamicModel($specifyModelFields);
                $specifyModel->addRule($specifyModelFields, 'safe');
                $specifyModel->setAttributes($specifyModelValues);
                if ($cache->get('status') == 'testing_type' && $specifyModel->load(Yii::$app->request->post())) {
                    $questions = Question::prepareQuestions($model->themes, $model->include_hard, $model->points == '' ? -1 : $model->points, $specifyModel->getAttributes());
                    $cache->flush();
                    if (empty($questions)) {
                        $model->addError('points', 'Мы не смогли составить вариант по заданным критериям. Пожалуйста добавьте ещё тем или уменьшите количество заданий в варианте работы');
                        return $this->render('setupQuestions', [
                            'model' => $model,
                        ]);
                    }
                    \Yii::$app->getSession()->set('xmlVariants', Json::encode($questions));
                    return $this->render('variants', [
                        'type' => $type,
                        'variants' => $questions,
                    ]);
                }
                $cache->set('status', 'testing_type');
                return $this->render('specifyTestingType', [
                    'setupModel' => $model,
                    'model' => $specifyModel,
                ]);
            }
            return $this->render('setupQuestions', [
                'model' => $model,
            ]);
        } catch (\yii\base\ErrorException $ex) {
            $cache->flush();
            return $this->redirect(Url::toRoute('site/questions'));
        }
    }

    public function actionLogin()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne([
                'login' => $model->login,
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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(Url::toRoute('site/login'));
        }
        $dataProvider = new ActiveDataProvider([
            'query' => QuestionContent::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('grid', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $questionContent = new QuestionContent();
        $question = new Question();
        $questionTheme = new QuestionTheme();
        $resQ = false;
        $resC = false;
        $resT = false;
        if ($question->load(Yii::$app->request->post())) {
            $resQ = $question->save();
        }
        if ($questionContent->load(Yii::$app->request->post())) {
            $questionContent->question_id = $question->id;
            $resC = $questionContent->save();
        }
        if ($resQ && $questionTheme->load(Yii::$app->request->post())) {
            $questionTheme->question_id = $question->id;
            $resT = $questionTheme->save();
        }
        if ($resQ && $resC && $resT) {
            return $this->redirect(Url::toRoute(['site/update', 'id' => $question->id]));
        }
        return $this->render('form', [
            'questionContent' => $questionContent,
            'question' => $question,
            'questionTheme' => $questionTheme,
        ]);
    }

    public function actionUpdate()
    {
        $questionId = Yii::$app->request->get('id');
        if (!empty($questionId)) {
            $questionContent = QuestionContent::findOne(['question_id' => $questionId]);
            $question = Question::findOne(['id' => $questionId]);
            $questionTheme = QuestionTheme::findOne(['question_id' => $questionId]);
        } else {
            return $this->redirect(Url::toRoute('site/admin'));
        }
        if ($questionContent->load(Yii::$app->request->post())) {
            $questionContent->save();
        }
        if ($questionTheme->load(Yii::$app->request->post())) {
            $questionTheme->save();
        }
        if ($question->load(Yii::$app->request->post())) {
            $question->save();
        }
        return $this->render('form', [
            'questionContent' => $questionContent,
            'question' => $question,
            'questionTheme' => $questionTheme,
        ]);
    }

    public function actionDelete()
    {
        $questionId = Yii::$app->request->get('id');
        $questionContent = QuestionContent::findOne(['question_id' => $questionId]);
        $question = Question::findOne(['id' => $questionId]);
        $questionTheme = QuestionTheme::findOne(['question_id' => $questionId]);
        if (!empty($questionContent)) {
            $questionContent->delete();
        }
        if (!empty($questionTheme)) {
            $questionTheme->delete();
        }
        if (!empty($question)) {
            $question->delete();
        }
        return $this->redirect(Url::toRoute('site/admin'));
    }

    public function actionMoodleXml()
    {
        $variants = Yii::$app->session->get('xmlVariants');
        $moodleXml = new \SimpleXMLElement('<?xml version="1.0"?><quiz></quiz>');
        try {
            $variants = Json::decode($variants);
            foreach ($variants as $variantNumber => $questions) {
                foreach ($questions as $questionArr) {
                    $question = Question::findOne(['id' => $questionArr['id']]);
                    if (empty($question)) {
                        continue;
                    }
                    $currentQuestionNode = $moodleXml->addChild('question');
                    if (in_array($question->content->testing_type, [Question::MULTIPLE_CHOICE, Question::ALTERNATIVE_CHOICE])) {
                        // указываю тип вопроса
                        $currentQuestionNode->addAttribute('type', 'multichoice');
                        if ($question->content->testing_type == Question::ALTERNATIVE_CHOICE) {
                            $currentQuestionNode->addChild('single', true);
                        }
                        // добавляю текст вопроса
                        $currentQuestionNode->addChild('name', "Вопрос №" . $question->id);
                        $currentQuestionNode->addChild('questiontext', $question->content->content);
                        // добавляю варианты ответа на вопрос
                        $answersOptions = $question->content->answersOptionsToArray();
                        $answers = $question->content->answerToArray();
                        foreach ($answersOptions as $answersOption) {
                            $answerNode = $currentQuestionNode->addChild('answer');
                            if (in_array($answersOption, $answers)) {
                                $answerNode->addAttribute('fraction', 1 / sizeof($answers));
                            } else {
                                $answerNode->addAttribute('fraction', 0);
                            }
                            $answerNode->addChild('text', $answersOption);
                        }
                        // включаю перемешивание ответов
                        $currentQuestionNode->addChild('shuffleanswers', true);
                    }
                    if ($question->content->testing_type == Question::MAPPING) {
                        // указываю тип вопроса
                        $currentQuestionNode->addAttribute('type', 'matching');
                        // добавляю текст вопроса
                        $currentQuestionNode->addChild('name', "Вопрос №" . $question->id);
                        $currentQuestionNode->addChild('questiontext', $question->content->content);
                        // добавляю варианты ответа на вопрос
                        $answers = $question->content->answerToArray();
                        for ($i = 0; $i < sizeof($answers); $i = $i + 2) {
                            $subquestionNode = $currentQuestionNode->addChild('subquestion');
                            $subquestionNode->addChild('text', $answers[$i]);
                            $answerNode = $subquestionNode->addChild('answer');
                            $answerNode->addChild('text', $answers[$i + 1]);
                        }
                        // включаю перемешивание ответов
                        $currentQuestionNode->addChild('shuffleanswers', true);
                    }
                    if ($question->content->testing_type == Question::FREE_FORM) {
                        // указываю тип вопроса
                        $currentQuestionNode->addAttribute('type', 'essay');
                        // добавляю текст вопроса
                        $currentQuestionNode->addChild('name', "Вопрос №" . $question->id);
                        $currentQuestionNode->addChild('questiontext', $question->content->content);
                        // у FREE_FORM нет ответа и автоматической проверки правильности, но формально их нужно указать
                        $answerNode = $currentQuestionNode->addChild('answer');
                        $answerNode->addAttribute('fraction', 0);
                        $answerNode->addChild('text');
                    }
                    if ($question->content->testing_type == Question::ADDITION) {
                        // указываю тип вопроса
                        $currentQuestionNode->addAttribute('type', 'shortanswer');
                        // добавляю текст вопроса
                        $currentQuestionNode->addChild('name', "Вопрос №" . $question->id);
                        $currentQuestionNode->addChild('questiontext', $question->content->content);
                        // добавляю ответы на вопрос
                        $answers = $question->content->answerToArray();
                        foreach ($answers as $answer) {
                            $answerNode = $currentQuestionNode->addChild('answer');
                            $answerNode->addAttribute('fraction', 100);
                            $answerNode->addChild('text', $answer);
                        }
                    }
                    if ($question->content->testing_type == Question::SEQUENCE) {
                        // указываю тип вопроса
                        $currentQuestionNode->addAttribute('type', 'matching');
                        // добавляю текст вопроса
                        $currentQuestionNode->addChild('name', "Вопрос №" . $question->id);
                        $currentQuestionNode->addChild('questiontext', $question->content->content);
                        // добавляю ответы на вопрос
                        $answers = $question->content->answerToArray();
                        foreach ($answers as $key => $answer) {
                            $subquestionNode = $currentQuestionNode->addChild('subquestion');
                            $subquestionNode->addChild('text', $key + 1);
                            $answerNode = $subquestionNode->addChild('answer');
                            $answerNode->addChild('text', $answer);
                        }
                        // включаю перемешивание ответов
                        $currentQuestionNode->addChild('shuffleanswers', true);
                    }
                }
            }
            //Yii::$app->session->remove('xmlVariants');
            return Yii::$app->response->sendContentAsFile($moodleXml->asXML(), 'variants_export' . time() . '.xml');
        } catch (yii\base\ErrorException|yii\base\InvalidArgumentException $ex) {
            echo "Пожалуйста закройте страницу и запустите выгрузку ещё раз.";
        } catch (RangeNotSatisfiableHttpException $e) {
            echo "Возникла проблема в синтаксисе при генериции xml файла";
        }
    }
}
