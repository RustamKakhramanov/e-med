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

class DestTemplateController extends \yii\web\Controller {

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
            'query' => \app\models\DestTemplate::find()
                ->where(['branch_id' => Yii::$app->user->identity->branch_id])
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
        $specs = \app\models\Speciality::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $model = new \app\models\DestTemplate();

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            
            $model->branch_id = Yii::$app->user->identity->branch_id;
            if ($model->save()) {
                $model->saveRel();
            }

            return $this->redirect('/' . $this->id);
        } else {

            return $this->render('add', [
                        'model' => $model,
                        'specs' => $specs
            ]);
        }
    }

    public function actionAddPrice() {
        $this->layout = 'ajax';
        return $this->render('add-price', [
        ]);
    }

    public function actionAddPriceAc() {
        $query = new Query;
        $query->select(['id', 'title', 'cost'])
                ->from(\app\models\Price::tableName())
                ->where(['=', 'deleted', 0])
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

    public function actionEdit($id) {
        $model = \app\models\DestTemplate::findOne(['id' => $id]);
        $specs = \app\models\Speciality::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $errors = ActiveForm::validate($model);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }
                if ($model->save()) {
                    $model->saveRel();
                }

                return $this->redirect('/' . $this->id);
            } else {
                return $this->render('add', [
                            'model' => $model,
                            'specs' => $specs
                ]);
            }
        }
    }
    
    public function actionDelete($id){
        $model = \app\models\DestTemplate::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->delete();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

}
