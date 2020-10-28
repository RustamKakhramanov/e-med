<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class BranchController extends \yii\web\Controller {

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
            'query' => \app\models\Branch::find()
                    ->where(['=', 'deleted', 0])
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

        $model = new \app\models\Branch();
        $model->setDefault();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            $model->extra = json_encode(Yii::$app->request->post('extra'));
            $model->save();

            return $this->redirect('/branch');
        } else {

            return $this->render('add', [
                        'model' => $model,
            ]);
        }
    }

    public function actionEdit($id) {
        $model = \app\models\Branch::findOne(['id' => $id]);

        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {

                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }
                $model->extra = json_encode(Yii::$app->request->post('extra'));
                $model->save();

                return $this->redirect('/branch');
            } else {

                return $this->render('add', [
                            'model' => $model,
                ]);
            }
        }
    }

    public function actionDelete($id) {
        $model = \app\models\Branch::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->delete();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionLoadExtensions($id) {

        $branch = \app\models\Branch::findOne(['id' => $id]);
        if (!$branch) {
            throw new NotFoundHttpException();
        }
        
        $loadedNumber = 0;

        $url = $branch->getExtraParam('aster');
        if ($url) {
            $client = new \WebSocket\Client($url);
            $client->send(json_encode([
                'action' => 'extensions',
                'data' => null
            ]));
            $items = json_decode($client->receive(), true);
            $client->close();

            if ($items) {
                $oldItems = \app\models\Extensions::find()
                        ->where(['branch_id' => $branch->id])
                        ->all();
                foreach ($oldItems as $ext) {
                    $ext->delete();
                }
            }

            foreach ($items as $item) {
                $ext = new \app\models\Extensions();
                $ext->setAttributes([
                    'name' => $item['name'],
                    'exten' => $item['extension'],
                    'branch_id' => $branch->id
                ]);
                $ext->save();
            }
            
            $loadedNumber = count($items);
        }

        echo $loadedNumber;
        exit;
    }

}
