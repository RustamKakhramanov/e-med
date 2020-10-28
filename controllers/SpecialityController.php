<?php

namespace app\controllers;

use app\models\PriceGroup;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SpecialityController extends \yii\web\Controller {

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
        $searchModel = new \app\models\SpecialitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {
        $model = new \app\models\Speciality();
        $priceGroups = PriceGroup::find()
            ->where([
                'branch_id' => Yii::$app->user->identity->branch_id,
                'deleted' => false
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
            $model->branch_id = Yii::$app->user->identity->branch_id;
            if ($model->save()) {
                $model->savePrice(Yii::$app->request->post('price', []));
            }

            return $this->redirect('/speciality');
        } else {

            return $this->render('add', [
                'model' => $model,
                'priceGroups' => $priceGroups
            ]);
        }
    }

    public function actionEdit($id) {
        $model = \app\models\Speciality::findOne(['id' => $id]);
        $priceGroups = PriceGroup::find()
            ->where([
                'branch_id' => Yii::$app->user->identity->branch_id,
                'deleted' => false
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();


        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {

                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }

                if ($model->save()) {
                    $model->savePrice(Yii::$app->request->post('price', []));
                }

                return $this->redirect('/speciality');
            } else {

                return $this->render('add', [
                    'model' => $model,
                    'priceGroups' => $priceGroups
                ]);
            }
        }
    }

    public function actionDelete($id) {
        $model = \app\models\Speciality::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->deleteSafe();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * поиск позиций прайса
     * @return array
     */
    public function actionPriceSearch() {
        $query = new Query;
        $query->select(['id', 'title', 'cost'])
            ->from(\app\models\Price::tableName())
            ->where([
                'deleted' => 0,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->andWhere(['ilike', 'title', Yii::$app->request->get('term')])
            ->orderBy(['title' => SORT_ASC])
            ->limit(10);

        $resp = [];
        foreach ($query->all() as $row) {
            $resp[$row['id']] = [
                'title' => $row['title'],
                'cost' => number_format($row['cost'], 2, ', ', ' ')
            ];;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionAddPrice() {
        $this->layout = 'ajax';
        return $this->render('add-price', []);
    }

    public function actionAddPriceAc() {
        $query = new yii\db\Query;
        $query->select(['id', 'title', 'cost'])
            ->from(\app\models\Price::tableName())
            ->where([
                'deleted' => 0,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
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

    public function actionAc(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $searchModel = new \app\models\SpecialitySearch([
            'name' => Yii::$app->request->get('q')
        ]);
        $dataProvider = $searchModel->search([]);

        foreach ($dataProvider->getModels() as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->name
            ];
        }

        return $result;
    }

    public function actionPicker() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\SpecialitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('picker/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
