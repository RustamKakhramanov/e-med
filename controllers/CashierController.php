<?php

namespace app\controllers;

use app\models\CashierSearch;
use app\models\Check;
use app\models\CheckSearch;
use app\models\Direction;
use app\models\DirectionItem;
use app\models\form\CashierCancelForm;
use app\models\form\CashierForm;
use app\models\Patients;
use app\models\report\CashierReport;
use app\models\User;
use Props\NotFoundException;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class CashierController extends \yii\web\Controller {

    public function behaviors() {
        return [
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
        $searchModel = new CashierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * просмотр неоплаченных направлений и формирование чека
     * @param $id
     * @return array|string
     * @throws NotFoundException
     */
    public function actionView($id) {
        $this->layout = 'ajax';
        $patient = Patients::findOne(['id' => $id]);
        if (!$patient) {
            throw new NotFoundException();
        }
        //модель формы
        $model = new CashierForm();
        $model->patient_id = $patient->id;

        //неоплаченные активные услуги
        $items = DirectionItem::find()
            ->joinWith('direction')
            ->where([
                Direction::tableName() . '.patient_id' => $patient->id,
                DirectionItem::tableName() . '.paid' => false,
                DirectionItem::tableName() . '.canceled' => false,
            ])
            ->orderBy([Direction::tableName() . '.created' => SORT_DESC])
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->services = Yii::$app->request->post('services', []);
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }
            $model->process();

            return $this->redirect(Yii::$app->request->post('back'));
        }

        return $this->render('view', [
            'patient' => $patient,
            'items' => $items,
            'model' => $model
        ]);
    }

    public function actionChecks() {
        $searchModel = new CheckSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('checks/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCheckPrint($id) {
        $this->layout = 'ajax';
        $model = Check::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundException();
        }

        if (!$model->webkassa_id) {
            throw new NotFoundException('Чек не был передан в Webkassa, печать невозможна');
        }

        return $this->render('checks/print', [
            'model' => $model
        ]);
    }

    public function actionCheckCancel($id) {
        $this->layout = 'ajax';
        $model = new CashierCancelForm();
        $model->check_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            $model->services = Yii::$app->request->post('services', []);
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }
            $model->process();
            return $this->redirect(Yii::$app->request->post('back'));
        }

        return $this->render('checks/cancel', [
            'model' => $model
        ]);
    }

    public function actionReport() {
        $searchModel = new CashierReport();
        $searchModel->setDefault();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('report/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionReportLoadCashiers($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $items = User::find()->where(['like', 'extra', '"cashbox_id":"' . $id . '"'])->all();

        return ArrayHelper::map($items, 'id', 'fio');
    }

    public function actionReportExport() {
        $searchModel = new CashierReport();
        $searchModel->setDefault();
        $searchModel->exportXls(Yii::$app->request->queryParams);
    }

    public function actionResendWebkassa($id) {
        $model = Check::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        $model->sendWebkassa();

        return $this->redirect(Yii::$app->request->referrer);
    }
}
