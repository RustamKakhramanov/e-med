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

class ScheduleController extends \yii\web\Controller {

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
            ]
        ];
    }

    public function actionIndex() {
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

        $doctors = \app\models\Doctor::find()
                ->andWhere([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy([
                    'last_name' => SORT_ASC,
                    'first_name' => SORT_ASC,
                    'middle_name' => SORT_ASC,
                ])
                ->all();

        $searchModel = new \app\models\ScheduleSearch();
        $data = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $data;
        }

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'data' => $data,
                    'specialities' => $specialities,
                    'subdivisions' => $subdivisions,
                    'doctors' => $doctors
        ]);
    }

    public function actionCut($id) {
        $event = \app\models\Event::findOne(['id' => $id]);

        //проверка на существование, статуса создания и соответсвия юзера
        if (!$event || $event->creation || $event->user_id != Yii::$app->user->identity->id) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
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

            $doctors = \app\models\Doctor::find()
                    ->andWhere([
                        'deleted' => 0,
                        'branch_id' => Yii::$app->user->identity->branch_id
                    ])
                    ->orderBy([
                        'last_name' => SORT_ASC,
                        'first_name' => SORT_ASC,
                        'middle_name' => SORT_ASC,
                    ])
                    ->all();

            $searchModel = new \app\models\ScheduleSearch();
            $data = $searchModel->search(Yii::$app->request->queryParams);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $data;
            }

            return $this->render('cut', [
                        'searchModel' => $searchModel,
                        'data' => $data,
                        'specialities' => $specialities,
                        'subdivisions' => $subdivisions,
                        'doctors' => $doctors,
                        'event' => $event
            ]);
        }
    }

    protected function _validatePaste($doctor_id = false, $date = false, $time = false) {
        //проверка доступной записи
        $available = false;

        if (isset($doctor_id, $date, $time)) {

            $formatValid = true;
            if (!preg_match('/^[1-9]+[0-9]*$/', $doctor_id)) {
                $formatValid = false;
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $formatValid = false;
            }

            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $formatValid = false;
            }

            if (!$formatValid) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $doctor = \app\models\Doctor::getById($doctor_id);
            if (!$doctor) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $docSchedule = current($doctor->getScheduleGrid($date, $date));
            foreach ($docSchedule['times'] as $cell) {
                if ($time == $cell['start'] && ($cell['type'] == \app\models\Doctor::TYPE_FREE || $cell['type'] == \app\models\Doctor::TYPE_PAST)) {
                    $available = true;
                    break;
                }
            }
        }

        return $available;
    }

    public function actionPaste() {
        $get = Yii::$app->request->get();
        $available = $this->_validatePaste($get['doctor_id'], $get['date'], $get['time']);
        if ($available) {
            $event = \app\models\Event::findOne(['id' => $get['id']]);
            $event->date = $get['date'] . ' ' . $get['time'];
            $event->doctor_id = $get['doctor_id'];
            $event->save();

            return $this->redirect('/schedule');
        } else {
            
        }
    }

    public function actionCancel($id) {
        $model = \app\models\Event::findOne(['id' => $id]);
        if ($model && $model->user_id == Yii::$app->user->identity->id) {
            $model->canceled = 1;
            $model->save(false);
        }
        exit;
    }

    public function actionCutAjax($id) {
        $this->layout = 'ajax';
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        $event = \app\models\Event::findOne(['id' => $id]);

        //проверка на существование, статуса создания и соответсвия юзера
        if (!$event || $event->creation || $event->user_id != Yii::$app->user->identity->id) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
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

            $doctors = \app\models\Doctor::find()
                    ->andWhere([
                        'deleted' => 0,
                        'branch_id' => Yii::$app->user->identity->branch_id
                    ])
                    ->orderBy([
                        'last_name' => SORT_ASC,
                        'first_name' => SORT_ASC,
                        'middle_name' => SORT_ASC,
                    ])
                    ->all();

            $searchModel = new \app\models\ScheduleSearchModal();

            if (isset($get['ScheduleSearchModal'])) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $data = $searchModel->search($get);
                return $data;
            } else {

                return $this->render('cut-ajax', [
                            'searchModel' => $searchModel,
                            'data' => $searchModel->search(Yii::$app->request->queryParams),
                            'specialities' => $specialities,
                            'subdivisions' => $subdivisions,
                            'doctors' => $doctors,
                            'event' => $event
                ]);
            }
        }
    }

    public function actionPasteAjax() {
        $get = Yii::$app->request->get();
        $available = $this->_validatePaste($get['doctor_id'], $get['date'], $get['time']);
        if ($available) {
            $event = \app\models\Event::findOne(['id' => $get['id']]);
            $event->date = $get['date'] . ' ' . $get['time'];
            $event->doctor_id = $get['doctor_id'];
            $event->save();
        }
        exit;
    }

}
