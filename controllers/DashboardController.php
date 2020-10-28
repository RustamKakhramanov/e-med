<?php

/**
 * Рабочий стол
 */

namespace app\controllers;

use app\models\DirectionItem;
use app\models\DoctorPrice;
use app\models\form\DirectionCancel;
use app\models\form\DirectionMaster;
use app\models\form\ReceptionForm;
use app\models\Patients;
use app\models\Reception;
use app\models\ScheduleSearch;
use app\models\Template;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\models\Doctor;

class DashboardController extends \yii\web\Controller {

    public function behaviors() {
        return [
            //BaseControllerBehavior::className(),
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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

    public function beforeAction($action) {
        if ($action->id == 'upload-image') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $searchModel = new \app\models\DirectionSearch();
        $searchModel->canceled = false;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $doctor = Doctor::findOne(['id' => Yii::$app->user->identity->doctor_id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'scheduleData' => $doctor->getScheduleGrid(date('d.m.Y'), date('d.m.Y'))
        ]);
    }

    public function actionLoadScheduleDay() {
        $this->layout = 'ajax';
        $doctor = Doctor::findOne(['id' => Yii::$app->user->identity->doctor_id]);

        return $this->render('schedule/day', [
            'scheduleData' => $doctor->getScheduleGrid(date('d.m.Y'), date('d.m.Y'))
        ]);
    }

    public function actionLoadScheduleDayData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $doctor = Doctor::findOne(['id' => Yii::$app->user->identity->doctor_id]);

        return $doctor->getScheduleGrid(Yii::$app->request->get('date'), Yii::$app->request->get('date'));
    }

    public function actionLoadScheduleWeek() {
        $this->layout = 'ajax';
        $scheduleSearch = new ScheduleSearch([
            'type' => 'week',
            'date_week' => date('d.m.Y', strtotime('monday this week')),
            'week_doctor_id' => Yii::$app->user->identity->doctor_id
        ]);

        return $this->render('schedule/week', [
            'scheduleData' => $scheduleSearch->search([])
        ]);
    }

    public function actionLoadScheduleWeekData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $scheduleSearch = new ScheduleSearch([
            'type' => 'week',
            'date_week' => Yii::$app->request->get('date'),
            'week_doctor_id' => Yii::$app->user->identity->doctor_id
        ]);

        return  $scheduleSearch->search([]);
    }

    public function actionCancel($id) {
        $this->layout = 'ajax';
        $directionItem = DirectionItem::findOne(['id' => $id]);
        if (!$directionItem) {
            throw new NotFoundHttpException();
        }
        $model = new DirectionCancel();
        $model->setDirection($directionItem);
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                return $errors;
            }

            if (Yii::$app->request->post('_sended')) {
                return [
                    'status' => (int)$model->save()
                ];
            }
        }

        return $this->render('cancel', [
            'model' => $model
        ]);
    }

    /**
     * @param $id направление
     */
    public function actionReception($id) {
        $directionItem = DirectionItem::findOne(['id' => $id]);
        if (!$directionItem) {
            throw new NotFoundHttpException();
        }

        $model = new ReceptionForm();
        $model->setDirItem($directionItem);

        $searchModel = new \app\models\TemplateSearch();
        $doctor = Doctor::findOne(['id' => Yii::$app->user->identity->doctor_id]);
        if (!$doctor) {
            throw new NotFoundHttpException();
        }
        $specIds = [];
        foreach ($doctor->doctorSpecialities as $rel) {
            $specIds[] = $rel->speciality_id;
        }
        $searchModel->docId = $doctor->id;
        $searchModel->specIds = $specIds;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $templates = $dataProvider->models;

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($model) + $model->commonValidate();

            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended') && !Yii::$app->request->get('autosave')) {
                return $errors;
            }

            if (Yii::$app->request->get('autosave')) {
                return $model->autosave();
            }

            $model->process();

            return $this->redirect(['dashboard/index']);
        }

        return $this->render('reception/index', [
            'directionItem' => $directionItem,
            'templates' => $templates,
            'model' => $model,
            'directions' => $directionItem->direction->patient->activeDirections
        ]);
    }

    /**
     * загрузить шаблон
     * @param $id
     * @return string
     */
    public function actionReceptionLoadTemplate() {
        $this->layout = 'ajax';
        $draft = Reception::findOne(['id' => Yii::$app->request->post('draft_id')]);
        $template = Template::findOne(['id' => Yii::$app->request->post('template_id')]);
        $directionItem = DirectionItem::findOne(['id' => Yii::$app->request->post('direction_item_id')]);
        if (!($template && $directionItem)) {
            throw new NotFoundHttpException();
        }

        $doctor = Doctor::findOne(['id' => Yii::$app->user->identity->doctor_id]);
        $templateVars = $template->flatVars;

        $templateValues = [];
        foreach ($templateVars as $var) {
            $templateValues[$var['id']] = null;
        }

        foreach ($draft->receptionVars as $var) {
            if (array_key_exists($var->var_id, $templateValues)) {
                $templateValues[$var->var_id] = $var->value;
            }
        }

        return $this->render('reception/template', [
            'template' => $template,
            'relValues' => $template->relValues($directionItem, $doctor),
            'templateVars' => $templateVars,
            'templateValues' => $templateValues
        ]);
    }

    public function actionReceptionDraftPrint($id) {
        $this->layout = 'print';
        $model = Reception::findOne(['id' => $id]);

        return $this->render('/reception/print', [
            'model' => $model
        ]);
    }

    public function actionReceptionLoadSidebar($id) {
        $this->layout = 'ajax';
        $patient = Patients::findOne(['id' => $id]);

        return $this->render('reception/_sidebar', [
            'patient_id' => $patient->id,
            'directions' => $patient->activeDirections
        ]);
    }

    /**
     * создать направления
     * @param $id пациент
     */
    public function actionCreateDirection($id) {
        $this->layout = 'ajax';
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
            if (Yii::$app->request->post('_sended')) {
                $model->process();
            }
            return $errors;
        }

        return $this->render('reception/direction/add', [
            'patient' => $patient,
            'direction' => false
        ]);
    }
}
