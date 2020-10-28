<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class SubdivisionController extends \yii\web\Controller {

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
                
        $searchModel = new \app\models\SubdivisionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {

        $model = new \app\models\Subdivision();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            $model->branch_id = Yii::$app->user->identity->branch_id;
            $model->save();

            return $this->redirect('/subdivision');
        } else {

            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

    public function actionEdit($id) {
        $model = \app\models\Subdivision::findOne(['id' => $id]);

        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {

                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }

                $model->save();

                return $this->redirect('/speciality');
            } else {

                return $this->render('add', [
                            'model' => $model,
                ]);
            }
        }
    }
    
    public function actionDelete($id) {
        $model = \app\models\Subdivision::findOne(['id' => $id]);
        
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

}
