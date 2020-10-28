<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class CheckController extends \yii\web\Controller {

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
        $searchModel = new \app\models\CheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $shift = \app\models\Shift::getCurrent();

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
                    'shift' => $shift
        ]);
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

    public function actionGetCancelItems($id) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $check = \app\models\Check::find()->where(['id' => $id])->one();

        if (!$check) {
            throw new NotFoundHttpException();
        }

        $items = [];
        foreach ($check->activeItems as $item) {
            $temp = $item->toArray();
            $temp['name'] = $item->direction->price->title;
            $temp['group'] = $item->direction->price->group->name;
            $temp['cost'] = $item->direction->cost;
            $items[] = $temp;
        }

        return $items;
    }

    public function actionCancelItems() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $parentCheck = false;
        $items = Yii::$app->request->post('items');
        if (count($items)) {
            $parentCheck = \app\models\Check::find()->where(['id' => $items[0]['check_id']])->one();
        }


        if ($parentCheck) {

            $shift = \app\models\Shift::getCurrent();

            $sum = 0;
            foreach (Yii::$app->request->post('items') as $item) {
                $sum += $item['cost'];
            }

            $backCheck = new \app\models\Check();
            $backCheck->setAttributes([
                'user_id' => Yii::$app->user->identity->id,
                'patient_id' => $parentCheck->patient_id,
                'sum' => $sum,
                'back_id' => $parentCheck->id
            ]);

            if ($shift) {
                $backCheck->shift_id = $shift->id;
            }

            $backCheck->save();

            foreach (Yii::$app->request->post('items') as $item) {
                $Item = new \app\models\CheckItems();
                $Item->setAttributes([
                    'check_id' => $backCheck->id,
                    'direction_id' => $item['direction_id']
                ]);
                $Item->save();
            }
            
            \app\models\ContractCalc::createFromCheck($backCheck);
        }

        return false;
    }

}
