<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use alexinator1\jta\ActiveQuery;

class DoctorController extends \yii\web\Controller {

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
                //'get-calend-schedule' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {

//        $res = \app\models\Doctor::find()->andWhere(['id' => 1])->one();
//        debug($res->specialities);
//        debug($res);
//        exit;

        $specialities = \app\models\Speciality::find()
                ->andWhere([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $subdivisions = \app\models\Subdivision::find()
                ->andWhere([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $searchModel = new \app\models\DoctorSearch([
            'is_fired' => null
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
                    'specialities' => $specialities,
                    'subdivisions' => $subdivisions
        ]);
    }

    public function actionAdd() {

        $subdivisions = \app\models\Subdivision::find()
                ->andWhere([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $specialities = \app\models\Speciality::find()
                ->andWhere([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $model = new \app\models\Doctor();
        $model->setDefault();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            if ($model->save()) {
                $model->saveRel();
                $model->saveSchedule();
                $model->savePrice(Yii::$app->request->post('price', []));
            }

            return $this->redirect('/' . $this->id);
        } else {

            return $this->render('add', [
                        'model' => $model,
                        'subdivisions' => $subdivisions,
                        'specialities' => $specialities
            ]);
        }
    }

    public function actionEdit($id) {
        $subdivisions = \app\models\Subdivision::find()
                ->where([
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $specialities = \app\models\Speciality::find()
                ->where([
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $model = \app\models\Doctor::getById($id);

        //$model->setDefault();
        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {

                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }

                if ($model->save()) {
                    $model->saveRel();
                    $model->saveSchedule();
                    $model->savePrice(Yii::$app->request->post('price', []));
                }

                return $this->redirect('/' . $this->id);
            } else {

                return $this->render('add', [
                            'model' => $model,
                            'subdivisions' => $subdivisions,
                            'specialities' => $specialities
                ]);
            }
        }
    }

    public function actionDelete($id) {
        $model = \app\models\Doctor::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionPeriodCalendSchedule() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $model = \app\models\Doctor::getById($post['id']);

        $start = strtotime($post['start']);
        $end = strtotime($post['end']);
        $data = [];

        foreach ($model->scheduleCalend as $item) {
            $d = strtotime($item['date']);
            if ($d >= $start && $d <= $end) {
                $data[] = [
                    'num' => $item['num'],
                    'enabled' => (bool) $item['enabled'],
                    'date' => $item['date'],
                    'times' => $item['data_items']
                ];
            }
        }

        return $data;
    }

    public function actionClearCalendSchedule() {
        $post = Yii::$app->request->post();
        $model = \app\models\Doctor::getById($post['id']);

        $start = strtotime($post['start']);
        $end = strtotime($post['end']);
        $data = [];

        foreach ($model->scheduleCalend as $item) {
            $d = strtotime($item['date']);
            if ($d >= $start && $d <= $end) {
                $item->delete();
            }
        }

        exit;
    }

    public function actionUploadPhoto() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = uniqid();
        $uploadedFile = \yii\web\UploadedFile::getInstanceByName('file');
        $uploadedFile->saveAs(Yii::getAlias('@app/web') . \app\models\Doctor::PHOTO_PATH . $name . '.' . $uploadedFile->extension);

        return [
            'name' => $name . '.' . $uploadedFile->extension,
            'url' => \app\models\Doctor::PHOTO_PATH . $name . '.' . $uploadedFile->extension
        ];
    }

    public function actionPicker() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\DoctorSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('picker/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAc() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $searchModel = new \app\models\DoctorSearch([
            'name_fio' => Yii::$app->request->get('q')
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

}
