<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class ReportController extends \yii\web\Controller {

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

        throw new NotFoundHttpException();
    }

    public function actionDay() {

        $get = Yii::$app->request->get();
        $date = date('Y-m-d');

        if (isset($get['date'])) {
            $date = date('Y-m-d', strtotime($get['date']));
        }

        $dirs = \app\models\Direction::find()
                ->where(['and',
                    'active = true',
                    'direction.branch_id = :branch',
                    'to_char(direction.created, \'YYYY-MM-DD\') = :date'
                        ], [
                    ':date' => $date,
                    ':branch' => Yii::$app->user->identity->branch_id
                ])
                ->joinWith('doctor')
                ->joinWith('patient')
                ->all();

        $data = [];
        foreach ($dirs as $key => $dir) {
            if (!isset($data[$dir->doctor_id])) {
                $data[$dir->doctor_id] = [
                    'doctor' => $dir->doctor->fio,
                    'sum' => [
                        'total' => 0,
                        'paid' => 0,
                    ],
                    'count' => 0,
                    'patients' => []
                ];
            }

            if (!isset($data[$dir->doctor_id]['patients'][$dir->patient_id])) {
                $data[$dir->doctor_id]['patients'][$dir->patient_id] = [
                    'patient' => $dir->patient->fio,
                    'sum' => [
                        'total' => 0,
                        'paid' => 0
                    ],
                    'count' => 0
                ];
            }

            $data[$dir->doctor_id]['patients'][$dir->patient_id]['sum']['total'] += $dir->cost * $dir->count;
            $data[$dir->doctor_id]['patients'][$dir->patient_id]['count'] ++;
            $data[$dir->doctor_id]['sum']['total'] += $dir->cost * $dir->count;
            $data[$dir->doctor_id]['count'] ++;

            if ($dir->paid) {
                $data[$dir->doctor_id]['patients'][$dir->patient_id]['sum']['paid'] += $dir->cost * $dir->count;
                $data[$dir->doctor_id]['sum']['paid'] += $dir->cost * $dir->count;
            }
        }

        //итоговые цифры
        $final = [
            'count' => 0,
            'total' => 0,
            'paid' => 0
        ];

        foreach ($data as $doc) {
            $final['count'] += $doc['count'];
            $final['total'] += $doc['sum']['total'];
            $final['paid'] += $doc['sum']['paid'];
        }

        return $this->render('day', [
                    'data' => $data,
                    'date' => $date,
                    'final' => $final
        ]);
    }

}
