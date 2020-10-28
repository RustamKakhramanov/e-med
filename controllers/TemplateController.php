<?php

namespace app\controllers;

use app\models\Doctor;
use app\models\Template;
use app\models\TemplateDoc;
use app\models\TemplateSpec;
use app\models\TemplateVar;
use app\models\TemplateVarGroup;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class TemplateController extends \yii\web\Controller {

    public function beforeAction($action) {
        if ($action->id == 'upload-image') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {

//        $specialities = \app\models\Speciality::find()
//                ->andWhere([
//                    'deleted' => 0,
//                    'branch_id' => Yii::$app->user->identity->branch_id
//                ])
//                ->orderBy(['name' => SORT_ASC])
//                ->all();
//
//        $doctors = \app\models\Doctor::find()
//                ->andWhere([
//                    'deleted' => 0,
//                    'branch_id' => Yii::$app->user->identity->branch_id
//                ])
//                ->orderBy([
//                    'last_name' => SORT_ASC,
//                    'first_name' => SORT_ASC,
//                    'middle_name' => SORT_ASC,
//                ])
//                ->all();
//
//        $model = \app\models\Template::createDraft(Yii::$app->user->identity->id);
//
//        if ($model->load(Yii::$app->request->post())) {
//
//            $errors = ActiveForm::validate($model, $model->attributes());
//
//            if (Yii::$app->request->isAjax) {
//                Yii::$app->response->format = Response::FORMAT_JSON;
//
//                return $errors;
//            }
//
//            $model->draft = false;
//            $model->save();
//
//            return $this->redirect('/' . $this->id);
//        } else {
//
//            return $this->render('add', [
//                        'model' => $model,
//                        'varTypes' => \app\models\TemplateVar::$typesAvailable,
//                        'specialities' => $specialities,
//                        'doctors' => $doctors,
//                        'relVars' => \app\models\TemplateVar::relVarsArray()
//            ]);
//        }

        $model = Template::find()
            ->where([
                'user_id' => Yii::$app->user->identity->id,
                'draft' => true
            ])
            ->one();

        if (!$model) { //создать черновик
            $model = new Template();
            $model->setAttributes([
                'user_id' => Yii::$app->user->identity->id,
                'branch_id' => Yii::$app->user->identity->branch_id,
                'created' => date('Y-m-d H:i:s'),
                'draft' => true,
                'deleted' => false
            ]);
            $model->save();
        }

        if ($model->load(Yii::$app->request->post())) {
            $errors = $model->commonValidate();
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            $model->draft = false;
            if ($model->save()) {
                $model->saveRels();
            }

            return $this->redirect(['template/index']);
        }

        return $this->render('add/index', [
            'model' => $model
        ]);
    }

    public function actionEdit($id) {
        $model = Template::findOne(['id' => $id]);

        if ($model->load(Yii::$app->request->post())) {
            $errors = $model->commonValidate();
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            if ($model->save()) {
                $model->saveRels();
            }

            return $this->redirect(['template/index']);
        }

        return $this->render('add/index', [
            'model' => $model
        ]);
    }

    public function actionDelete($id) {
        $model = \app\models\Template::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException();
        } else {
            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * создать переменную
     * @param type $id ид шаблона
     * @return type
     */
    public function actionVarAdd($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new \app\models\TemplateVar();
        $data = Yii::$app->request->post();

        $model->setAttributes([
            'name' => $data['name'],
            'type' => $data['type']
        ]);

        if (isset($data['extra'])) {
            $model->extra = json_encode($data['extra']);
        } else {
            $model->extra = '{}';
        }

        $model->template_id = $id;
        $model->save();


        $template = \app\models\Template::find()->where(['id' => $id])->one();

        return $template->varsArray;
    }

    /**
     * редактировать
     * @param type $id
     */
    public function actionVarEdit($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $var = \app\models\TemplateVar::find()->where(['id' => $id])->one();

        $data = Yii::$app->request->post();

        $var->setAttributes([
            'name' => $data['name'],
            'type' => $data['type']
        ]);

        if (isset($data['extra'])) {
            $var->extra = json_encode($data['extra']);
        } else {
            $var->extra = '{}';
        }

        $var->save();

        return $var->template->varsArray;
    }

    /**
     * удалить переменную
     * @param type $id
     */
    public function actionVarDelete($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $var = \app\models\TemplateVar::find()->where(['id' => $id])->one();
        $var->deleteSafe();

        return $var->template->varsArray;
    }

    public function actionUploadImage($id) {
        $template = \app\models\Template::find()->where(['id' => $id])->one();
        if ($template) {
            $name = $id . '_' . uniqid();
            $uploadedFile = \yii\web\UploadedFile::getInstanceByName('upload');
            $uploadedFile->saveAs(Yii::getAlias('@webroot') . '/uploads/template/' . $name . '.' . $uploadedFile->extension);

            return $this->renderPartial('upload-image', [
                'url' => '/uploads/template/' . $name . '.' . $uploadedFile->extension,
                'funcNum' => Yii::$app->request->get('CKEditorFuncNum')
            ]);
        }
    }

    /**
     * добавить строку специализации
     */
    public function actionAccessAddRow() {
        $this->layout = 'ajax';
        $model = new TemplateSpec();

        return $this->render('add/_access_row', [
            'model' => $model
        ]);
    }

    /**
     * добавить строку врача
     */
    public function actionAccessAddDocRow() {
        $this->layout = 'ajax';
        $model = new TemplateDoc();

        return $this->render('add/_access_doc_row', [
            'model' => $model
        ]);
    }

    /**
     * добавить показатель
     * @param $id
     * @return array|string
     */
    public function actionAddVar($id) {
        $this->layout = 'ajax';

        $template = Template::findOne(['id' => $id]);
        $model = new TemplateVar();
        $model->template_id = $template->id;
        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model) + $model->extraValidate();
            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            $model->saveExtra(Yii::$app->request->post('extra', []));
            $model->save();

            return $this->render('add/vars/index', [
                'model' => $template,
                'customTab' => true
            ]);
        }

        return $this->render('add/vars/form', [
            'model' => $model
        ]);
    }

    /**
     * изменить показатель
     * @param $id
     * @return array|string
     */
    public function actionEditVar($id) {
        $this->layout = 'ajax';

        $model = TemplateVar::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model) + $model->extraValidate();
            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            $model->saveExtra(Yii::$app->request->post('extra', []));
            $model->save();

            return $this->render('add/vars/index', [
                'model' => $model->template,
                'customTab' => true
            ]);
        }

        return $this->render('add/vars/form', [
            'model' => $model
        ]);
    }

    /**
     * удалить показатель
     * @param $id
     * @return string
     */
    public function actionRemoveVar($id) {
        $this->layout = 'ajax';
        $model = TemplateVar::findOne(['id' => $id]);
        $template = $model->template;
        if ($model) {
            $model->delete();
        }

        return $this->render('add/vars/index', [
            'model' => $template,
            'customTab' => true
        ]);
    }

    /**
     * список показателей
     * @param $id
     * @return string
     */
    public function actionVarList($id) {
        $this->layout = 'ajax';
        $template = Template::findOne(['id' => $id]);
        $customTab = Yii::$app->request->get('customTab', false);

        return $this->render('add/vars/index', [
            'model' => $template,
            'customTab' => $customTab
        ]);
    }

    /**
     * добавить группу
     * @param $id
     * @return array|string
     */
    public function actionAddGroup($id) {
        $this->layout = 'ajax';
        $template = Template::findOne(['id' => $id]);
        $model = new TemplateVarGroup();
        $model->template_id = $template->id;

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            $model->save();

            return $this->render('add/vars/index', [
                'model' => $template,
                'customTab' => true
            ]);
        }

        return $this->render('add/vars/form-group', [
            'model' => $model
        ]);
    }

    /**
     * изменить группу
     * @param $id
     * @return array|string
     */
    public function actionEditGroup($id) {
        $this->layout = 'ajax';
        $model = TemplateVarGroup::findOne(['id' => $id]);

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax && !Yii::$app->request->post('_sended')) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $errors;
            }

            $model->save();

            return $this->render('add/vars/index', [
                'model' => $model->template,
                'customTab' => true
            ]);
        }

        return $this->render('add/vars/form-group', [
            'model' => $model
        ]);
    }

    /**
     * удалить группу
     * @param $id
     */
    public function actionRemoveGroup($id) {
        $this->layout = 'ajax';
        $model = TemplateVarGroup::findOne(['id' => $id]);
        $template = $model->template;
        $model->delete();

        return $this->render('add/vars/index', [
            'model' => $template,
            'customTab' => true
        ]);
    }

    /**
     * добавить значение селекта
     */
    public function actionVarAddSelect() {
        $this->layout = 'ajax';

        return $this->render('add/vars/_select_row', [
            'value' => ''
        ]);
    }
}
