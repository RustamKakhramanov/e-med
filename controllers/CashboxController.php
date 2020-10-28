<?php

namespace app\controllers;

use app\models\Cashbox;
use Props\NotFoundException;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class CashboxController extends \yii\web\Controller {

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
        $models = Cashbox::find()
            ->where([
                'branch_id' => Yii::$app->user->identity->branch_id,
                'deleted' => false
            ])
            ->orderBy([
                'name' => SORT_DESC
            ])
            ->all();

        return $this->render('index', [
            'models' => $models
        ]);
    }

    public function actionAdd() {
        $model = new Cashbox();
        $model->branch_id = Yii::$app->user->identity->branch_id;

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            $model->save();

            return $this->redirect(['index']);
        }

        return $this->render('add',[
            'model' => $model
        ]);
    }

    public function actionEdit($id) {
        $model = Cashbox::findOne([
            'id' => $id,
            'deleted' => false,
            'branch_id' => Yii::$app->user->identity->branch_id
        ]);
        if (!$model) {
            throw new NotFoundException();
        }

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            $model->save();

            return $this->redirect(['index']);
        }

        return $this->render('add',[
            'model' => $model
        ]);
    }

    public function actionDelete($id) {
        $model = Cashbox::findOne([
            'id' => $id,
            'deleted' => false,
            'branch_id' => Yii::$app->user->identity->branch_id
        ]);
        if (!$model) {
            throw new NotFoundException();
        }

        $model->deleted = true;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionPicker() {
        $this->layout = 'ajax';
        $models = Cashbox::find()
            ->where([
                'branch_id' => Yii::$app->user->identity->branch_id,
                'deleted' => false
            ])
            ->andFilterWhere(['ilike', 'name', Yii::$app->request->get('name')])
            ->orderBy([
                'name' => SORT_DESC
            ])
            ->all();

        return $this->render('picker/index', [
            'models' => $models
        ]);
    }

    public function actionAc() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $models = Cashbox::find()
            ->where([
                'branch_id' => Yii::$app->user->identity->branch_id,
                'deleted' => false
            ])
            ->andFilterWhere(['ilike', 'name', Yii::$app->request->get('q')])
            ->orderBy([
                'name' => SORT_DESC
            ])
            ->all();
        foreach ($models as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->name
            ];
        }

        return $result;
    }
}
