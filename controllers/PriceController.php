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

class PriceController extends \yii\web\Controller {

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

        $searchModel = new \app\models\PriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->prepare();

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('_rows', [
                        'dataProvider' => $dataProvider,
            ]);
        } else {

            return $this->render('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'countFindRecord' => $dataProvider->query->count(),
                        'groups' => $this->_groupsJson(),
                        'types' => \app\models\Price::$types
            ]);
        }
    }

    public function actionAdd() {
        $model = new \app\models\Price();
        $model->setDefault();

        $groups = \app\models\PriceGroup::find()
                ->where([
                    'deleted' => false,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        } else {

            return $this->render('add', [
                        'model' => $model,
                        'groups' => $groups
            ]);
        }
    }

    public function actionGroupAc() {
        $term = Yii::$app->request->get('q');

        $query = new Query;
        $query->select('group')
                ->from(\app\models\Price::tableName())
                ->where(['ilike', 'group', $term])
                ->andWhere(['branch_id' => Yii::$app->user->identity->branch_id])
                ->groupBy('group')
                ->limit(10);
        $data = [];
        foreach ($query->all() as $item) {
            $data[] = $item['group'];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
    }

    public function actionEdit($id) {
        $model = \app\models\Price::find()->where(['id' => $id])->one();

        $groups = \app\models\PriceGroup::find()
                ->where([
                    'deleted' => false,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        if (!$model || $model->deleted) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

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
                        'groups' => $groups
            ]);
        }
    }

    public function actionDelete($id) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = \app\models\Price::findOne(['id' => $id]);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            $model->deleteSafe();
            //return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionAddGroup() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        if ($post['item']['id']) {
            $group = \app\models\PriceGroup::find()->where(['id' => $post['item']['id']])->one();
            if (!$group) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } else {
            $group = new \app\models\PriceGroup();
        }

        $group->name = $post['item']['name'];
        $group->branch_id = Yii::$app->user->identity->branch_id;
        $group->save();

        return $this->_groupsJson($post['item']['id']);
    }

    public function actionDeleteGroup() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $group = \app\models\PriceGroup::find()->where(['id' => Yii::$app->request->post('id')])->one();
        if ($group) {
            $group->deleteSafe();
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->_groupsJson(Yii::$app->request->post('id'));
    }

    protected function _groupsJson($selected_id = null) {
        $data = [];
        $groups = \app\models\PriceGroup::find()
                ->where([
                    'deleted' => false,
                    'branch_id' => Yii::$app->user->identity->branch_id
                ])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        foreach ($groups as $group) {
            $temp = $group->toArray();
            $temp['count'] = $group->priceCount;
            $temp['items'] = [];
            if ($selected_id && $selected_id == $group->id) {
                foreach ($group->items as $item) {
                    $temp['items'][] = $item->toArray();
                }
            }
            $data[] = $temp;
        }

        return $data;
    }

    public function actionImport() {

        $csv = array_map(function($e) {
            return str_getcsv($e, ';');
        }, file(Yii::getAlias('@app') . '/price.csv'));

        $groupName = null;
        $group;

        $types = [
            'Инструментальные исследования' => 2,
            'Прием специалиста' => 0,
            'Манипуляции' => 3,
            'Лабораторные исследования' => 1,
        ];

        foreach ($csv as $row) {
            if ($row[0] && $row[1]) {

                if ($groupName != $row[1]) {
                    $group = \app\models\PriceGroup::find()
                            ->where([
                                'deleted' => 0,
                                'branch_id' => Yii::$app->user->identity->branch_id
                            ])
                            ->andWhere(['ilike', 'name', $row[1]])
                            ->one();

                    if (!$group) {
                        $group = new \app\models\PriceGroup();
                        $group->name = $row[1];
                        $group->branch_id = Yii::$app->user->identity->branch_id;
                        $group->save();
                    }
                }

                if (isset($types[$row[3]])) {

                    $item = new \app\models\Price();
                    $item->setAttributes([
                        'group_id' => $group->id,
                        'title' => $row[0],
                        'type' => $types[$row[3]],
                        'cost' => (float) str_replace(',', '.', str_replace(' ', '', $row[2])),
                        'branch_id' => Yii::$app->user->identity->branch_id
                    ]);
                    $item->save();
                }
            }
        }

        exit;
    }

    public function actionPicker() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\PriceSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('picker/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAc() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $searchModel = new \app\models\PriceSearch([
            'title' => Yii::$app->request->get('q')
        ]);
        $dataProvider = $searchModel->search([]);

        foreach ($dataProvider->getModels() as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->title
            ];
        }

        return $result;
    }

    /**
     * модал с выбором нескольких
     */
    public function actionFewPicker() {
        $this->layout = 'ajax';
        $searchModel = new \app\models\PriceSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        $groups = PriceGroup::find()
            ->where([
                'deleted' => false,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->orderBy([
                'name' => SORT_ASC
            ])
            ->all();

        if (Yii::$app->request->isPost) {
            return $this->render('few-picker/_right', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
        }

        return $this->render('few-picker/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'groups' => $groups
        ]);
    }
}
