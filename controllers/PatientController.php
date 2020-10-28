<?php

namespace app\controllers;

use app\models\Direction;
use app\models\DirectionItem;
use app\models\form\DirectionMaster;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class PatientController extends \yii\web\Controller {

    public function behaviors() {
        return [
            //BaseControllerBehavior::className(),
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
            return $this->redirect(['@web/login']);
        },
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
        $searchModel = new \app\models\PatientsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {

        $model = new \app\models\Patients();
        $model->setDefault();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            if ($model->save()) {
                $model->saveContacts()->saveContracts();
            }

            return $this->redirect('/patient');
        } else {

            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

    public function actionEdit($id) {
        $model = \app\models\Patients::findOne(['id' => $id]);

        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $model->new = 0;
                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }

                if ($model->save()) {
                    $model->saveContacts()->saveContracts();
                }

                return $this->redirect('/patient');
            } else {

                return $this->render('add', [
                            'model' => $model,
                ]);
            }
        }
    }

    public function actionDelete($id) {
        $model = \app\models\Patients::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * электронная мед карта (история направлений)
     */
    public function actionEmc($id) {
        $patient = \app\models\Patients::findOne(['id' => $id]);
        if (!$patient) {
            throw new NotFoundHttpException;
        }
        $data = [];
        $dirs = \app\models\Direction::find()
                ->joinWith('reception')
                ->joinWith('price')
                ->joinWith('doctor')
                ->where([
                    'direction.patient_id' => $patient->id,
                    'direction.closed' => true,
                    'reception.draft' => false
                ])
                ->orderBy(['created' => SORT_ASC])
                ->all();

        return $this->render('emc', [
                    'patient' => $patient,
                    'dirs' => $dirs,
                    'priceTypeData' => [
                        'labels' => \app\models\Price::$types,
                        'icons' => \app\models\Price::$icons
                    ]
        ]);
    }

    public function actionPicker() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\PatientsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('picker/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAc() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $searchModel = new \app\models\PatientsSearch([
            'name_query' => Yii::$app->request->get('q')
        ]);
        $dataProvider = $searchModel->search([]);

        foreach ($dataProvider->getModels() as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->fio
            ];
        }

        return $result;
    }

    public function actionDirection($id) {
        $patient = \app\models\Patients::findOne(['id' => $id]);

        if (!$patient || $patient->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $searchModel = new \app\models\DirectionSearch([
            'patient_id' => $patient->id
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('direction/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'patient' => $patient
        ]);
    }

    public function actionDirectionAdd($id) {
        $patient = \app\models\Patients::findOne(['id' => $id]);

        if (!$patient || $patient->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new DirectionMaster();
        $model->patient_id = $patient->id;
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            $errors = ActiveForm::validate($model) + $model->commonValidate();
            if (Yii::$app->request->isAjax) {
                return $errors;
            }

            $model->process();

            return $this->redirect(['patient/direction', 'id' => $patient->id]);
        }

        return $this->render('direction/add', [
            'patient' => $patient,
            'direction' => false
        ]);
    }

    public function actionDirectionAddRow() {
        $this->layout = 'ajax';
        $model = new DirectionItem();

        return $this->render('direction/_add_row', ['model' => $model]);
    }

    public function actionDirectionAddFewRow() {
        $this->layout = 'ajax';
        $models = [];
        foreach (Yii::$app->request->post('items', []) as $item) {
            $model = new DirectionItem();
            $model->price_id = $item['id'];
            $models[] = $model;
        }

        return $this->render('direction/_add_few_row', [
            'models' => $models
        ]);
    }

    public function actionDirectionEdit($id) {
        $direction = Direction::findOne(['id' => $id]);
        if (!$direction) {
            throw new NotFoundHttpException();
        }

        if (!$direction->canEdit) {
            throw new NotFoundHttpException('Направление недоступно для редактирования');
        }

        $patient = $direction->patient;

        $model = new DirectionMaster();
        $model->patient_id = $patient->id;
        $model->direction_id = $direction->id;
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            $errors = ActiveForm::validate($model) + $model->commonValidate();
            if (Yii::$app->request->isAjax) {
                return $errors;
            }

            $model->process();

            return $this->redirect(['patient/direction', 'id' => $patient->id]);
        }

        return $this->render('direction/add', [
            'patient' => $patient,
            'direction' => $direction
        ]);
    }
}