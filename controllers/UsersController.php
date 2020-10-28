<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class UsersController extends \yii\web\Controller {

    public function behaviors() {
        return [
            //BaseControllerBehavior::className(),
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            'admin',
                            'root'
                        ],
                    ],
                ],
//                'denyCallback' => function ($rule, $action) {
//            return $this->redirect(['@web/login']);
//        },
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
        if (Yii::$app->user->identity->isRoot) {
            $dataProvider = new ActiveDataProvider([
                'query' => \app\models\User::find()
                        ->orderBy('username')
                        ->groupBy('id'),
                'pagination' => [
                    'pageSize' => 999
                ]
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => \app\models\User::find()
                        ->where(['!=', 'role_id', \app\models\User::ROLE_ROOT])
                        ->andWhere(['branch_id' => Yii::$app->user->identity->branch_id])
                        ->orderBy('username')
                        ->groupBy('id'),
                'pagination' => [
                    'pageSize' => 999
                ]
            ]);
        }

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'countFindRecord' => $dataProvider->query->count(),
        ]);
    }

    public function actionAdd() {
        $branches = [];
        if (Yii::$app->user->identity->isRoot) {
            $branches = \app\models\Branch::find()
                    ->where(['=', 'deleted', 0])
                    ->groupBy(['id'])
                    ->orderBy(['name' => SORT_ASC])
                    ->all();
        } else {
            $branches[] = Yii::$app->user->identity->branch;
        }

        $model = new \app\models\User();
        $model->setDefault();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {

            $errors = ActiveForm::validate($model);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }

            if ($model->password_new) {
                $model->setPassword($model->password_new);
                //$model->generateAuthKey();
            }
            
            $model->extra = json_encode(Yii::$app->request->post('extra'));
            $model->save();

            return $this->redirect('/' . $this->id);
        } else {

            return $this->render('add', [
                        'model' => $model,
                        'branches' => $branches
            ]);
        }
    }

    /**
     * поиск доктора
     * @return array
     */
    public function actionDoctorSearch() {
        $query = \app\models\Doctor::find()->limit(10);

        $query->andFilterWhere([
            'deleted' => false
        ]);

        if (!Yii::$app->user->identity->isRoot) {
            $query->andFilterWhere([
                Yii::$app->user->identity->branch_id
            ]);
        }

        $fio = explode(' ', Yii::$app->request->get('term'));
        $groups = ['or'];
        $params = [];
        $fields = ['last_name', 'first_name', 'middle_name'];

        foreach ($fio as $key => $word) {
            $params[':fio' . $key] = $word . '%';
        }

        foreach ($fields as $field) {
            $group = ['or'];
            foreach ($params as $key => $param) {
                $group[] = \app\models\Doctor::tableName() . '.' . $field . ' ilike ' . $key;
            }
            $groups[] = $group;
        }

        $query->andWhere($groups, $params);

        $resp = [];
        foreach ($query->all() as $row) {
            $resp[$row['id']] = [
                'title' => $row->initials
            ];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $resp;
    }

    public function actionEdit($id) {
        $branches = [];
        if (Yii::$app->user->identity->isRoot) {
            $branches = \app\models\Branch::find()
                    ->where(['=', 'deleted', 0])
                    ->groupBy(['id'])
                    ->orderBy(['name' => SORT_ASC])
                    ->all();
        } else {
            $branches[] = Yii::$app->user->identity->branch;
        }

        $model = \app\models\User::findOne(['id' => $id]);
        $model->scenario = \app\models\User::SCENARIO_UPDATE;

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        } else {
            if ($model->load(Yii::$app->request->post())) {

                $errors = ActiveForm::validate($model);

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $errors;
                }

                if ($model->password_new) {
                    $model->setPassword($model->password_new);
                    //$model->generateAuthKey();
                }
                
                $model->extra = json_encode(Yii::$app->request->post('extra'));
                $model->save();

                return $this->redirect('/' . $this->id);
            } else {

                return $this->render('add', [
                            'model' => $model,
                            'branches' => $branches
                ]);
            }
        }
    }

}
