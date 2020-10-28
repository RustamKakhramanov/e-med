<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ContractorController extends \yii\web\Controller {

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

        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\Contractor::find()
                    ->where([
                        'deleted' => false,
                        'branch_id' => Yii::$app->user->identity->branch_id
                    ])
                    ->groupBy(['id'])
                    ->orderBy(['name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 999
            ]
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {

        $model = new \app\models\Contractor();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            $model->branch_id = Yii::$app->user->identity->branch_id;
            $model->save();

            return $this->redirect('/' . $this->id);
        } else {

            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

}
