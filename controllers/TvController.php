<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class TvController extends \yii\web\Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            'admin',
                            'root'
                        ],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $list = \app\models\TvSchedule::find()
                ->where(['branch_id' => Yii::$app->user->identity->branch_id])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        return $this->render('index', [
                    'list' => $list
        ]);
    }

    public function actionAdd() {
        $model = new \app\models\TvSchedule();
        $model->setDefault();
        $model->branch_id = Yii::$app->user->identity->branch_id;

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            if ($model->save()) {
                return $this->redirect('/tv');
            } else {
                dd($model->errors);
                exit;
            }
        } else {
            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

    public function actionEdit($id) {
        $model = \app\models\TvSchedule::findOne(['id' => $id]);

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            if ($model->save()) {
                return $this->redirect('/tv');
            } else {
                dd($model->errors);
                exit;
            }
        } else {
            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

    public function actionLoadTemplate($name) {
        $class = '\app\models\tv\template\\' . $name;
        $template = new $class;
        $this->layout = 'ajax';

        return $this->render('template/' . strtolower($name), [
                    'template' => $template
        ]);
    }

    public function actionWidgetForm() {
        $name = Yii::$app->request->get('w');
        $data = json_decode(Yii::$app->request->post('data', '[]'), true);
        $class = '\app\models\tv\widget\\' . ucfirst($name);
        $widget = new $class;
        $this->layout = 'ajax';

        if ($data && isset($data[$name])) {
            $widget->setAttributes($data[$name]);
        }

        if (Yii::$app->request->post('_from_form')) {
            $widget->load(Yii::$app->request->post());
            $errors = ActiveForm::validate($widget);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!Yii::$app->request->post('_sended')) {
                return $errors;
            } else {
                return $widget->toArray();
            }
        }

        return $this->render('widget/' . $name, [
                    'widget' => $widget
        ]);
    }

    public function actionView($code) {

        $model = \app\models\TvSchedule::findOne(['code' => $code]);
        if (!$model) {
            throw new NotFoundHttpException;
        }

        $this->layout = 'tv_public';

        return $this->render('public/' . strtolower($model->template), [
                    'model' => $model,
                    'widgets' => $model->widgetsList
        ]);
    }

    public function actionDelete($id) {
        $model = \app\models\TvSchedule::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->delete();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDischargedRow() {
        $this->layout = 'ajax';
        
        return $this->render('widget/discharged/row');
    }
    
    public function actionDischargedUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = uniqid();
        $uploadedFile = \yii\web\UploadedFile::getInstanceByName('file');
        $uploadedFile->saveAs(Yii::getAlias('@app/web/uploads/discharged/') . $name . '.' . $uploadedFile->extension);

        return [
            'name' => $name . '.' . $uploadedFile->extension,
            'url' => '/uploads/discharged/' . $name . '.' . $uploadedFile->extension
        ];
    }

}
