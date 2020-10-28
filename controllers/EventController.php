<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class EventController extends \yii\web\Controller {

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
        ];
    }

    public function actionIndex() {
        $searchModel = new \app\models\EventSearch();
        $searchModel->setDefault();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    /**
     * проверка доступной записи
     * @return boolean
     */
    protected function _validateCell($doctor_id = false, $date = false, $time = false) {
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
                if ($time == $cell['start']) {
                    if ($cell['type'] == \app\models\Doctor::TYPE_FREE) {
                        $available = true;
                        break;
                    }
                }
            }
        }

        return $available;
    }

    /**
     * создание события из расписания
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionAdd() {

        $get = Yii::$app->request->get();

        if ($this->_validateCell($get['doctor_id'], $get['date'], $get['time'])) {
            $model = new \app\models\Event();
            $model->setDefault();
            $model->doctor_id = $get['doctor_id'];
            $model->date = $get['date'] . ' ' . $get['time'] . ':00';
            $model->save(false);

            if (Yii::$app->request->get('back')) {
                return $this->redirect('/' . $this->id . '/edit/' . $model->id . '?back=' . urlencode(Yii::$app->request->get('back')));
            } else {
                return $this->redirect('/' . $this->id . '/edit/' . $model->id);
            }
        } else {
            throw new NotFoundHttpException('Выбранное время не доступно');
        }
    }

    /**
     * создание нового пациента
     * @return array
     */
    public function actionNewPatient() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $resp = [
            'errors' => [],
            'result' => []
        ];

        $model = new \app\models\EventNewPatient();
        $model->load(Yii::$app->request->post());
        $resp['errors'] = ActiveForm::validate($model);

        if (!$resp['errors']) {
            $model->save();
            $resp['result'] = [
                'id' => $model->id,
                'birthday' => date('d.m.Y', strtotime($model->birthday)),
                'fio' => $model->fio,
                'sex' => $model->sex
            ];
        }

        return $resp;
    }

    /**
     * пролонг поля updated при создании из расписания
     */
    public function actionAddProlong($id) {
        $model = \app\models\Event::findOne(['id' => $id]);
        if ($model && $model->user_id == Yii::$app->user->identity->id) {
            $model->save(false);
        }
        exit;
    }

    /**
     * отмена события
     * @param type $id
     */
    public function actionCancel($id) {

        $model = \app\models\Event::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        //если событие в режиме создания
        if ($model->creation) {
            //if ($model->user_id != Yii::$app->user->identity->id) {
            $model->delete();
        } else {
            $model->canceled = 1;
            $model->save(false);
        }

        return $this->redirect(urldecode(Yii::$app->request->get('back')));
    }

    /**
     * обработка drag drop
     */
    public function actionDrag() {

        $errors = [];
        $post = Yii::$app->request->post();
        $model = \app\models\Event::findOne(['id' => $post['id']]);

//проверка на существование, статуса создания и соответсвия юзера
        if (!$model || $model->user_id != Yii::$app->user->identity->id) {
            $errors[] = 'Произошла ошибка';
        } else {
//проверка на доступость времени
            $available = $this->_validateCell($post['doctor_id'], $post['date'], $post['time']);

            if ($available) {
                $model->date = $post['date'] . ' ' . $post['time'];
                $model->doctor_id = $post['doctor_id'];
                $model->save(false);
            } else {
                $errors[] = 'Время не доступно';
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $errors;
    }

    public function actionEdit($id) {
        $model = \app\models\Event::findOne(['id' => $id]);
        $patient = new \app\models\EventNewPatient();
        $patient->setDefault();

        $prevCreation = $model->creation;
//        if ($prevCreation) {
//            $model->scenario = \app\models\Event::SCENARIO_NEW;
//        }
        //проверка на существование
        if (!($model && !$model->deleted)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        //проверка на создаваемость
        if ($model->creation && $model->user_id != Yii::$app->user->identity->id) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            //$model->scenario = \app\models\Event::SCENARIO_FIND;
            $errors = array_merge($errors, ActiveForm::validate($model));

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            //флаг что событие создано
            $model->creation = false;
            $model->save();
            if ($prevCreation) {
                $model->saveRel();
            }

            return $this->redirect(Yii::$app->request->get('back') ? urldecode(Yii::$app->request->get('back')) : '/event');
        } else {

            $patientSearchModel = new \app\models\PatientsSearch();

            return $this->render('add', [
                        'model' => $model,
                        'patient' => $patient,
                        'patientSearchModel' => $patientSearchModel
            ]);
        }
    }

    /**
     * поиск позиций прайса
     * @return array
     */
    public function actionPriceSearch() {
        $query = new \yii\db\Query();
        $query->select(['id', 'title', 'cost'])
                ->from(\app\models\Price::tableName())
                ->where([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->andWhere(['ilike', 'title', Yii::$app->request->get('term')])
                ->orderBy(['title' => SORT_ASC])
                ->limit(10);

        $resp = [];
        foreach ($query->all() as $row) {
            $resp[$row['id']] = [
                'title' => $row['title'],
                'cost' => $row['cost']
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionDoctorMiniGrid($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $doc = \app\models\Doctor::find()->where(['id' => $id])->one();

        if (!$doc) {
            throw new NotFoundHttpException;
        }

        return $doc->miniGrid;
    }

    public function actionAddPrice() {
        $this->layout = 'ajax';
        return $this->render('add-price', []);
    }

    public function actionAddPriceAc() {
        $query = new yii\db\Query;
        $query->select(['id', 'title', 'cost'])
                ->from(\app\models\Price::tableName())
                ->where([
                    'deleted' => 0,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->andWhere(['ilike', 'title', Yii::$app->request->get('term')])
                ->orderBy(['title' => SORT_ASC])
                ->limit(10);

        $resp = [];
        foreach ($query->all() as $row) {
            $resp[$row['id']] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'value' => $row['title'],
                'cost' => number_format($row['cost'], 2, ', ', ' ')
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionTest() {
        $r = rand(1, 5);
        //sleep(rand(1, 3));
        echo '<h1>asdasd' . uniqid() . '</h1>';
        for ($i = 0; $i < $r; $i++) {
            echo '<p>' . uniqid() . '</p>';
        }
        exit;
    }

    public function actionPatientSearchAc() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $searchModel = new \app\models\PatientsSearch();
        $dataProvider = $searchModel->search([
            'PatientsSearch' => [
                'name_fio' => Yii::$app->request->get('term')
            ]
        ]);

        $resp = [];
        foreach ($dataProvider->getModels() as $row) {
            $resp[$row->id] = [
                'id' => $row->id,
                'value' => $row->fio,
                'sex' => (int) $row->sex,
                'birthday' => $row->birthdayPrint
            ];
        }

        return $resp;
    }

    public function actionPatientSearch() {
        $searchModel = new \app\models\PatientsSearch();
        //$searchModel->li
        $query = Yii::$app->request->get('PatientsSearch');
        if ($query) {
            $searchModel->$query['quick_field'] = $query['quick_value'];
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false, 10);

        $this->layout = 'ajax';
        return $this->render('/patient/quick-search/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->totalCount,
        ]);
    }

    public function actionPatientAdd() {
        $model = new \app\models\EventNewPatient();
        $model->setDefault();

        if (Yii::$app->request->get('term')) {
            $model->last_name = urldecode(Yii::$app->request->get('term'));
        }

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($model);
            if ($errors) {
                return $errors;
            } else {
                if (Yii::$app->request->post('_sended')) {
                    $model->save();
                    //$model->saveContacts();
                    return [
                        'id' => $model->id,
                        'birthday' => date('d.m.Y', strtotime($model->birthday)),
                        'fio' => $model->fio,
                        'sex' => $model->sex ? 'М' : 'Ж'
                    ];
                }
            }
        } else {
            $this->layout = 'ajax';
            return $this->render('/patient/quick-add/index', [
                        'model' => $model,
            ]);
        }
    }

    public function actionAddAjax() {
        $this->layout = 'ajax';
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();

        if ($post) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = \app\models\Event::findOne(['id' => $post['Event']['id']]);
            $model->load($post);
            //$model->scenario = \app\models\Event::SCENARIO_NEW;            
            $errors = ActiveForm::validate($model);
            if ($errors) {
                return $errors;
            }
            if (isset($post['_sended'])) {
                $model->creation = false;
                if ($model->save()) {
                    $model->saveRel();
                    return $model;
                }
            }
        } else {
            if ($this->_validateCell($get['doctor_id'], $get['date'], $get['time'])) {
                $model = new \app\models\Event();
                $model->setDefault();
                $model->doctor_id = $get['doctor_id'];
                $model->date = $get['date'] . ' ' . $get['time'] . ':00';
                $model->creation = true;
                $model->save(false);

                return $this->render('add-ajax', [
                            'model' => $model,
                                //'patient' => $patient,
                                //'patientSearchModel' => $patientSearchModel
                ]);
            } else {
                throw new NotFoundHttpException('Выбранное время не доступно');
            }
        }
    }

    /**
     * отмена события
     * @param type $id
     */
    public function actionCancelAjax($id) {
        $model = \app\models\Event::findOne(['id' => $id]);
        if ($model) {
            //если событие в режиме создания
            if ($model->creation) {
                //if ($model->user_id != Yii::$app->user->identity->id) {
                $model->delete();
            } else {
                $model->canceled = 1;
                $model->save(false);
            }
        }
    }

    public function actionEditAjax($id) {
        $this->layout = 'ajax';
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();

        $model = \app\models\Event::findOne(['id' => $id]);

        //проверка на существование
        if (!($model && !$model->deleted)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        //проверка на создаваемость
        if ($model->creation && $model->user_id != Yii::$app->user->identity->id) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($post) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($post);
            $errors = ActiveForm::validate($model);

            if (isset($post['_sended']) && !$errors) {
                $model->creation = false;
                if ($model->save()) {
                    $model->saveRel();
                    return $model;
                }
            } else {
                return $errors;
            }
        } else {
            return $this->render('add-ajax', [
                        'model' => $model,
                            //'patient' => $patient,
                            //'patientSearchModel' => $patientSearchModel
            ]);
        }
    }

    public function actionLog($id) {
        $model = \app\models\Event::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('log', [
                    'model' => $model
        ]);
    }

}
