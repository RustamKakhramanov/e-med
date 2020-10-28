<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'login-operator', 'logout', 'error', 'contact', 'test'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'prolong'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
            return $this->redirect('/login');
        },
            ]
        ];
    }

    public function actions() {
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

    public function actionIndex() {
        return Yii::$app->user->isGuest ? $this->redirect('/login') : $this->redirect('/' . current(\app\models\User::$menu[Yii::$app->user->identity->role_id]));
    }

    public function actionLogin() {

        $this->layout = 'login';

        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/' . current(\app\models\User::$menu[Yii::$app->user->identity->role_id]));
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->user->role_id == \app\models\User::ROLE_OPERATOR && !$model->number) {
                $numbers = $model->user->asterNumbers;
                if ($numbers) {
                    return $this->render('login-select-number', [
                                'model' => $model,
                                'numbers' => $numbers
                    ]);
                }
            }

            //поиск активных сессий с таким логином
            $exist = \app\models\UserSession::checkUniqUser($model->user);
            if ($exist) {
                return $this->render('login', [
                            'model' => $model,
                            'error' => Yii::$app->request->isPost,
                            'error_msg' => 'Существует активная сессия'
                ]);
            }

            $model->login();
            $model->user->createSession();

            return $this->goBack();
        }

        return $this->render('login', [
                    'model' => $model,
                    'error' => Yii::$app->request->isPost
        ]);
    }

    public function actionLoginOperator() {
        $this->layout = 'login';

        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/' . current(\app\models\User::$menu[Yii::$app->user->identity->role_id]));
        }

        $model = new LoginForm();
        $model->load(Yii::$app->request->post());

        //поиск активных сессий с таким логином
        if ($model->number) {
            $exist = \app\models\UserSession::checkUniqNumber($model->number);
        } else {
            $exist = false;
        }
        if ($exist) {
            $numbers = $model->user->asterNumbers;
            return $this->render('login-select-number', [
                        'model' => $model,
                        'error' => Yii::$app->request->isPost,
                        'error_msg' => $model->number . ' уже используется',
                        'numbers' => $numbers
            ]);
        }


        $model->login(false);
        $model->user->createSession($model->number ? $model->number : null);

        return $this->goBack();
    }

    public function actionLogout() {
        $data = Yii::$app->session->get('usess');
        if ($data) {
            $sess = \app\models\UserSession::findOne(['id' => $data['sess_id']]);
            $sess->closed = true;
            $sess->save();
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProlong() {
        $data = Yii::$app->session->get('usess');
        if ($data) {
            $sess = \app\models\UserSession::findOne(['id' => $data['sess_id']]);
            $sess->save();
        }
    }

}
