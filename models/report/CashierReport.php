<?php
/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 24.07.2018
 * Time: 12:03
 */

namespace app\models\report;

use app\models\Cashbox;
use app\models\Check;
use app\models\CheckItem;
use app\models\Direction;
use app\models\DirectionItem;
use app\models\Doctor;
use app\models\Patients;
use app\models\Price;
use app\models\Shift;
use app\models\User;
use yii\data\ActiveDataProvider;
use Yii;
use yii\helpers\ArrayHelper;

class CashierReport extends DirectionItem {

    const XLS_TEMPLATE = '@app/data/report/cashier-report-template.xlsx';

    public $date_start,
        $date_end,
        $author_id,
        $doctor_id,
        $cashbox_id,
        $cashier_id;

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['date_start'] = 'Дата с';
        $labels['date_end'] = 'Дата по';
        $labels['author_id'] = 'Кто направил';
        $labels['doctor_id'] = 'Кто провел';
        $labels['cashbox_id'] = 'Касса';
        $labels['cashier_id'] = 'Кассир';

        return $labels;
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['date_start', 'date_end'], 'safe'],
            [['author_id', 'doctor_id', 'cashbox_id', 'cashier_id'], 'integer']
        ];
    }

    public function setDefault() {
        $this->date_start = date('d.m.Y');
        $this->date_end = date('d.m.Y');
    }

    public function getCashboxes() {
        $items = Cashbox::find()
            ->where([
                'deleted' => false,
                'branch_id' => Yii::$app->user->identity->branch_id
            ])
            ->orderBy([
                'name' => SORT_ASC
            ])
            ->all();

        return ArrayHelper::map($items, 'id', 'name');
    }

    public function getCashiersList() {
        $items = User::find()->where(['like', 'extra', '"cashbox_id":"' . $this->cashbox_id . '"'])->all();
        return ArrayHelper::map($items, 'id', 'fio');
    }

    public function search($params) {
        $query = self::find();
//        $query->leftJoin(Price::tableName(), Price::tableName() . '.id = ' . self::tableName() . '.price_id');
//        $query->leftJoin(Doctor::tableName(), Doctor::tableName() . '.id = ' . self::tableName() . '.doctor_id');
//        $query->leftJoin(CheckItem::tableName(), CheckItem::tableName() . '.direction_item_id = ' . self::tableName() . '.id');
//        $query->leftJoin(Check::tableName(), CheckItem::tableName() . '.check_id = ' . Check::tableName() . '.id');
//        $query->leftJoin(Patients::tableName(), Patients::tableName() . '.id = ' . Check::tableName() . '.patient_id');
//        $query->leftJoin(Shift::tableName(), Shift::tableName() . '.id = ' . Check::tableName() . '.shift_id');
//        $query->leftJoin(Cashbox::tableName(), Cashbox::tableName() . '.id = ' . Shift::tableName() . '.cashbox_id');
        $query
            ->joinWith('checkItems')
            ->joinWith('direction')
            ->joinWith('doctor')
            ->joinWith('price')
            ->joinWith('checkItems.check')
            ->joinWith('checkItems.check.patient')
            ->joinWith('checkItems.check.shift')
            ->joinWith('checkItems.check.shift.cashbox');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id' => [
                    'label' => '#',
                    'asc' => [
                        self::tableName() . '.id' => SORT_ASC,
                    ],
                    'desc' => [
                        self::tableName() . '.id' => SORT_DESC
                    ]
                ],
                'patients_name' => [
                    'label' => 'Пациент',
                    'asc' => [
                        Patients::tableName() . '.last_name' => SORT_ASC,
                        Patients::tableName() . '.first_name' => SORT_ASC,
                        Patients::tableName() . '.middle_name' => SORT_ASC
                    ],
                    'desc' => [
                        Patients::tableName() . '.last_name' => SORT_DESC,
                        Patients::tableName() . '.first_name' => SORT_DESC,
                        Patients::tableName() . '.middle_name' => SORT_DESC,
                    ],
                ],
                'doctor_fio' => [
                    'label' => 'Специалист',
                    'asc' => [
                        Doctor::tableName() . '.search_field' => SORT_ASC,
                    ],
                    'desc' => [
                        Doctor::tableName() . '.search_field' => SORT_DESC
                    ]
                ],
                'user.username' => [
                    'label' => 'Пользователь',
                    'asc' => [User::tableName() . '.username' => SORT_ASC],
                    'desc' => [User::tableName() . '.username' => SORT_DESC]
                ]
            ],
            'defaultOrder' => [
                'id' => SORT_DESC
            ]
        ]);

        $this->load($params);

        $query->andWhere([
            Direction::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id,
            self::tableName() . '.paid' => true
        ]);

        $query->andWhere(['>=', Check::tableName() . '.created', date('Y-m-d 00:00:00', strtotime($this->date_start))]);
        $query->andWhere(['<=', Check::tableName() . '.created', date('Y-m-d 23:59:59', strtotime($this->date_end))]);

        if ($this->cashbox_id) {
            $query->andWhere([
                Cashbox::tableName() . '.id' => $this->cashbox_id
            ]);
        }

        $query->groupBy([
            self::tableName() . '.id'
        ]);

        return $dataProvider;
    }

    public function exportXls($params) {
        $dataProvider = $this->search($params);

        $type = \PHPExcel_IOFactory::identify(Yii::getAlias(self::XLS_TEMPLATE));
        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load(Yii::getAlias(self::XLS_TEMPLATE));
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        //шапка
        $objWorksheet->getCellByColumnAndRow(0, 3)->setValue(date('d.m.Y', strtotime($this->date_start)));
        $objWorksheet->getCellByColumnAndRow(1, 3)->setValue(date('d.m.Y', strtotime($this->date_end)));
        $cashboxName = 'Все';
        if ($this->cashbox_id) {
            $cashbox = Cashbox::findOne(['id' => $this->cashbox_id]);
            $cashboxName = $cashbox->name;
        }
        $objWorksheet->getCellByColumnAndRow(2, 3)->setValue($cashboxName);
        $cashierName = 'Все';
        if ($this->cashier_id) {
            $user = User::findOne(['id' => $this->cashier_id]);
            $cashierName = $user->fio;
        }
        $objWorksheet->getCellByColumnAndRow(3, 3)->setValue($cashierName);
        //таблица
        $rowNumber = 6;
        foreach ($dataProvider->models as $model) {
            $objWorksheet->getCellByColumnAndRow(0, $rowNumber)->setValue($model->numberPrint);
            $objWorksheet->getCellByColumnAndRow(1, $rowNumber)->setValue($model->serviceName);
            $objWorksheet->getCellByColumnAndRow(2, $rowNumber)->setValue($model->summ);
            $objWorksheet->getCellByColumnAndRow(3, $rowNumber)->setValue($model->direction->user->fio);
            $objWorksheet->getCellByColumnAndRow(4, $rowNumber)->setValue($model->doctor_id ? $model->doctor->fio : '-');

            $rowNumber++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $type);
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename=' . 'dump-' . date('Y-m-d H:i:s') . '.xlsx');
        $objWriter->save('php://output');
        exit;
    }
}