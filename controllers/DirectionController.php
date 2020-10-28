<?php

namespace app\controllers;

use app\models\Direction;
use app\models\DirectionItem;
use app\models\Doctor;
use app\models\DoctorPrice;
use app\models\form\DirectionMaster;
use app\models\Patients;
use app\models\Price;
use app\models\PriceGroup;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use WebSocket\Client;

class DirectionController extends \yii\web\Controller {

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
        $searchModel = new \app\models\DirectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionAdd() {

        $get = Yii::$app->request->get();
        $patient = false;
        if (isset($get['patient_id'])) {
            $patient = \app\models\Patients::findOne(['id' => $get['patient_id']]);
            if (!$patient) {
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }

        $model = new \app\models\Direction();
        $formData = \app\models\Direction::itemsPatient($get['patient_id']);
        $shift = \app\models\Shift::getCurrent();

        $priceGroups = [];
        foreach (\app\models\PriceGroup::find()
                     ->where([
                         'deleted' => 0,
                         'branch_id' => Yii::$app->user->identity->branch_id
                     ])
                     ->orderBy(['name' => SORT_ASC])
                     ->all() as $group) {
            $priceGroups[] = [
                'id' => $group->id,
                'group' => $group->name,
                'count' => $group->priceCount
            ];
        }

        return $this->render('add', [
            'model' => $model,
            'patient' => $patient,
            'priceGroups' => $priceGroups,
            'formData' => $formData,
            'shift' => $shift
        ]);
    }

    /**
     * поиск позиций прайса
     * @return array
     */
    public function actionPriceSearch() {

        $prices = \app\models\Price::find()
            ->where([
                'deleted' => 0,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->andWhere(['ilike', 'title', Yii::$app->request->get('term')])
            ->orderBy(['title' => SORT_ASC])
            ->limit(10)
            ->all();

        $resp = [];
        foreach ($prices as $price) {
            $resp[$price->id] = [
                'id' => $price->id,
                'title' => $price->title,
                'cost' => $price->cost,
                'group' => $price->group->name
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionPriceGroup() {
//        $query = new \yii\db\Query;
//        $query->select(['id', 'title', 'cost', 'group'])
//                ->from(\app\models\Price::tableName())
//                ->where(['=', 'deleted', 0])
//                ->andWhere(['=', 'group', Yii::$app->request->get('group')])
//                ->orderBy(['title' => SORT_ASC])
//                ->limit(999);
//
//        $resp = [];
        $group = \app\models\PriceGroup::find()->where(['id' => Yii::$app->request->get('id')])->one();

        if ($group) {
            foreach ($group->items as $row) {
                $resp[$row['id']] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'cost' => $row['cost'],
                    'group' => $group->name
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $resp;
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function actionCancel($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $Direction = \app\models\Direction::find()->where(['id' => $id])->one();

        if ($Direction && $Direction->user_id == Yii::$app->user->identity->id) {
            $Direction->delete();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * создание/сохранение направлений
     * @return type
     */
    public function actionSaveFew() {
        $post = Yii::$app->request->post();
        $Patient = \app\models\Patients::find()->where(['id' => $post['patient_id']])->one();
        $defaultContract = $Patient->defaultContract;

        foreach ($post['items'] as $key => $item) {
            if ($item['direction_id']) {
                $Direction = \app\models\Direction::getById($item['direction_id']);
                $Direction->count = $item['count'];
                $Direction->contract_id = isset($item['contract_id']) ? $item['contract_id'] : $defaultContract->id;
                $Direction->doctor_id = isset($item['doctor_id']) ? $item['doctor_id'] : null;
            } else {
                $Direction = new \app\models\Direction([
                    'patient_id' => $Patient->id,
                    'user_id' => Yii::$app->user->identity->id,
                    'created' => date('Y-m-d H:i:s'),
                    'price_id' => $item['id'],
                    'cost' => $item['cost'],
                    'count' => $item['count'],
                    'state' => 1,
                    'event_id' => isset($item['item_id']) ? $item['item_id'] : null,
                    'doctor_id' => isset($item['doctor_id']) ? $item['doctor_id'] : null,
                    'contract_id' => isset($item['contract_id']) ? $item['contract_id'] : $defaultContract->id,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ]);

                //сохранение флага у услуги события
                if (isset($item['event_price'])) {
                    $EventPrice = \app\models\EventPrice::find()->where(['id' => $item['event_price']])->one();
                    if ($EventPrice) {
                        $EventPrice->dir = true;
                        $EventPrice->save(false);
                    }
                }
            }
            $Direction->save(false);

            $post['items'][$key]['direction_id'] = $Direction->id;
            $post['items'][$key]['state'] = $Direction->state;
            $post['items'][$key]['paid'] = $Direction->paid;
            $post['items'][$key]['contract_id'] = $Direction->contract_id;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $post['items'];
    }

    /**
     * отменить направления
     */
    public function actionCancelFew() {
        $post = Yii::$app->request->post();

        foreach ($post['items'] as $key => $item) {

            if (isset($item['event_price'])) {
                $EventPrice = \app\models\EventPrice::find()->where(['id' => $item['event_price']])->one();
                if ($EventPrice) {
                    $EventPrice->canceled = true;
                    $EventPrice->save(false);
                }
            }

            if ($item['direction_id']) {
                $Direction = \app\models\Direction::find()->where(['id' => $item['direction_id']])->one();
                if ($Direction && $Direction->user_id == Yii::$app->user->identity->id) {
                    $Direction->delete();
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    /**
     * оплата направлений
     * @return type
     */
    public function actionPay() {
        $post = Yii::$app->request->post();

        $directions = [];
        foreach ($post['items'] as $key => $item) {
            $Direction = \app\models\Direction::getById($item['direction_id']);
            $Direction->paid = 1;
            $Direction->save(false);
            $directions[] = $Direction;
        }

        $sum = \app\models\Check::createFromDirections($post['patient_id'], $directions);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    /**
     * поиск доктора
     * @return array
     */
    public function actionDoctorSearch() {
        $query = \app\models\Doctor::find()->limit(10);
        $query->andFilterWhere([
            'deleted' => false,
            'branch_id' => Yii::$app->user->identity->branch_id
        ]);

        $fio = explode(' ', Yii::$app->request->get('term'));
        $groups = ['or'];
        $params = [];
        $fields = ['last_name', 'first_name', 'middle_name'];

        foreach ($fio as $key => $word) {
            $params[':fio' . $key] = $word . '%';
        }

        foreach ($fields as $field) {
            $group = ['or'];
            foreach ($params as $key => $param) {
                $group[] = \app\models\Doctor::tableName() . '.' . $field . ' ilike ' . $key;
            }
            $groups[] = $group;
        }

        $query->andWhere($groups, $params);

        $resp = [];
        foreach ($query->all() as $row) {
            $resp[$row['id']] = [
                'title' => $row->initials
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionShiftToggle() {

        $shift = \app\models\Shift::getCurrent();

        if ($shift) {
            $shift->close();
            $shift = new \app\models\Shift();
        } else {
            $shift = new \app\models\Shift();
            $shift->setAttributes([
                'user_id' => Yii::$app->user->identity->id,
                'start' => date('Y-m-d H:i:s')
            ]);
            $shift->save();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $shift->toArray();
    }

    public function actionSendDevice() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();

        $uid = $post['uid'];
        $items = [];
        foreach ($post['items'] as $item) {
            $items[] = [
                'name' => $item['title'],
                'cost' => $item['cost'],
                'count' => $item['count']
            ];
        }

        return \app\models\DeviceTask::createCheck($post['uid'], $items);
    }

    /**
     * $id patient id
     */
    public function actionMaster($id = null) {
        $model = new DirectionMaster();
        if ($id) {
            $patient = Patients::findOne(['id' => $id]);
            if (!$patient) {
                throw new NotFoundHttpException;
            }
            $model->patient_id = $id;
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            $errors = ActiveForm::validate($model) + $model->commonValidate();
            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                return $errors;
            }

            if (Yii::$app->request->post('_sended')) {
                $model->process();
            }

            return [];
        }

        return $this->render('master/index', [
            'model' => $model
        ]);
    }

    public function actionAddRow() {
        $this->layout = 'ajax';
        $model = new DirectionItem();

        return $this->render('master/item', ['model' => $model]);
    }

    public function actionLoadHistory($id) {
        $this->layout = 'ajax';
        $items = DirectionItem::find()
            ->joinWith('direction')
            ->where([
                'patient_id' => $id,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->orderBy([
                'created' => SORT_DESC
            ])
            ->all();

        return $this->render('master/history', [
            'items' => $items
        ]);
    }

    public function actionMasterLoadService($id) {
        $data = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $items = Doctor::find()
            ->joinWith('doctorPrices')
            ->where([
                DoctorPrice::tableName() . '.price_id' => $id
            ])
            ->orderBy([
                Doctor::tableName() . '.last_name' => SORT_ASC,
                Doctor::tableName() . '.first_name' => SORT_ASC,
                Doctor::tableName() . '.middle_name' => SORT_ASC
            ])
            ->all();
        $data['doctors'] = ArrayHelper::map($items, 'id', 'fio');
        $model = Price::findOne(['id' => $id]);
        $data['cost'] = $model->cost;

        return $data;
    }

    public function actionMasterViewHistory($id) {
        $this->layout = 'ajax';
        $model = DirectionItem::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $this->render('master/history-view', [
            'model' => $model
        ]);
    }

    public function actionPayment() {

        return $this->render('payment/index');
    }
}
