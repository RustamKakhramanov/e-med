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

class ContractController extends \yii\web\Controller {

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
            'query' => \app\models\Contract::find()
                    ->where([
                        'deleted' => false,
                        'branch_id' => Yii::$app->user->identity->branch_id
                    ])
                    ->orderBy('name')
                    ->groupBy('id'),
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

        throw new NotFoundHttpException('//todo');

        $model = new \app\models\Contract();

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

    public function actionEdit($id) {

        throw new NotFoundHttpException('//todo');

        $model = \app\models\Contract::findOne(['id' => $id]);

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

                return $this->redirect('/' . $this->id);
            } else {

                return $this->render('add', [
                            'model' => $model,
                ]);
            }
        }
    }

    public function actionDelete($id) {
        $model = \app\models\Contract::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {

            if ($model->typical && $model->main) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDoMain($id) {
        $model = \app\models\Contract::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {

            if ($model->typical && $model->main) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }

            $model->setMainTypical();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

}
