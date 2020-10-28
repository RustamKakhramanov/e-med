<?php

/**
 * онлайн запись с сайта
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class ApiController extends \yii\web\Controller {

    public $branch;

    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);

        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset(Yii::$app->request->queryParams['api_key'])) {
            $branch = \app\models\Branch::findOne(['api_key' => Yii::$app->request->queryParams['api_key']]);
            if ($branch) {
                $this->branch = $branch;
            } else {
                throw new \yii\base\InvalidParamException('invalid api_key');
            }
        } else {
            throw new \yii\base\InvalidParamException('invalid api_key');
        }
    }

    public function behaviors() {
        return [];
    }

    /**
     * список специализаций доступных для записи
     * @return type
     */
    public function actionSpecList() {
        $doctors = \app\models\Doctor::find()
                ->joinWith('specialities')
                ->where([
                    'doctor.deleted' => false,
                    'doctor.fired' => false,
                    'doctor_speciality.show_schedule' => 1,
                    'speciality.branch_id' => $this->branch->id
                ])
                ->all();

        $spec_ids = [];
        foreach ($doctors as $doc) {
            foreach ($doc['doctorSpecialities'] as $ds) {
                if (!in_array($ds->speciality_id, $spec_ids)) {
                    $spec_ids[] = $ds->speciality_id;
                }
            }
        }

        $list = \app\models\Speciality::find()
                ->where(['id' => $spec_ids])
                ->orderBy(['name' => SORT_ASC])
                ->all();

        $resp = [];
        foreach ($list as $item) {
            $resp[] = [
                'id' => $item->id,
                'name' => $item->name
            ];
        }

        return $resp;
    }

    /**
     * врачи с расписанием
     * @param type $id
     */
    public function actionDoctorList($id) {
        $spec = \app\models\Speciality::findOne(['id' => $id]);
        $resp = [];
        $date = Yii::$app->request->get('date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        foreach ($spec->doctors as $doc) {
            if (!$doc->deleted && !$doc->fired) {
                $schedule = current($doc->getScheduleGrid($date, $date));
                if ($schedule['periods']) {
                    $item = $doc->toArray();
                    $item['schedule'] = $schedule;
                    $resp[] = $item;
                }
            }
        }

        return $resp;
    }

    /**
     * врачи с расписанием
     * @param type $id
     */
    public function actionDoctorSingle($id) {
        $doc = \app\models\Doctor::findOne(['id' => $id]);
        $resp = [];
        $date = Yii::$app->request->get('date');
        if (!$date) {
            $date = date('Y-m-d');
        }

        if ($doc && !$doc->deleted) {
            $schedule = $doc->getScheduleGrid($date, date('Y-m-d', strtotime('+6 day', strtotime($date))));
            $item = $doc->toArray();
            $item['schedule'] = $schedule;
            $resp = $item;
        }

        return $resp;
    }

    /**
     * создание события
     * @return type
     */
    public function actionCreateEvent() {
        $get = Yii::$app->request->get();
                
        $doctor = \app\models\Doctor::getById($get['doctor_id']);
        $docSchedule = current($doctor->getScheduleGrid(date('Y-m-d', strtotime($get['date'])), date('Y-m-d', strtotime($get['date']))));

        $available = false;
        foreach ($docSchedule['times'] as $cell) {
            if (date('H:i', strtotime($get['date'])) == $cell['start']) {
                if ($cell['type'] == \app\models\Doctor::TYPE_FREE) {
                    $available = true;
                    break;
                }
            }
        }

        if (!$available) {
            return [
                'status' => false
            ];
        }

        $patient = \app\models\Patients::find()
                ->andWhere(['deleted' => 0])
                ->andWhere(['ilike', 'first_name', $get['firstname']])
                ->andWhere(['ilike', 'last_name', $get['lastname']])
                ->andWhere(['=', 'birthday', date('Y-m-d', strtotime($get['birthday']))])
                ->one();

        if (!$patient) {
            $patient = new \app\models\EventNewPatient();
            $patient->setDefault();
            $patient->setAttributes([
                'first_name' => $get['firstname'],
                'last_name' => $get['lastname'],
                'middle_name' => $get['middlename'],
                'birthday' => $get['birthday'],
                'phone' => $get['phone'],
                'sex' => $get['sex']
            ]);
            $patient->save();
        }

        $event = new \app\models\Event();
        $event->setDefault();
        $admin = \app\models\User::findOne([
                    'role_id' => \app\models\User::ROLE_ADMIN,
                    'branch_id' => $this->branch->id
        ]);
        $event->setAttributes([
            'type' => 1,
            'branch_id' => $this->branch->id,
            'doctor_id' => $get['doctor_id'],
            'patient_id' => $patient->id,
            'user_id' => $admin->id,
            'date' => $get['date'],
            'creation' => false
        ]);

        return [
            'status' => $event->save()
        ];
    }
   
    /**
     * обратный звонок
     */
    public function actionCallMe(){        
        $get = Yii::$app->request->get();
        $obj = new \app\models\ApiCall();
        $obj->setAttributes([
            'branch_id' => $this->branch->id,
            'name' => $get['name'],
            'theme' => $get['theme'],
            'date' => $get['date'],
            'number' => preg_replace('/[^0-9]/', '', str_replace('+7', '8', $get['phone']))
        ]);
        
        return [
            'status' => $obj->save()
        ];
    }
    
    /**
     * запись лога
     * @param type $text
     */
    protected function _log($text, $print = false) {
        $fp = fopen(__DIR__ . '/../runtime/logs/api.log', 'a');
        fwrite($fp, '[' . date('d.m.Y H:i:s') . '] ' . print_r($text, 1) . "\n");
        fclose($fp);
        if ($print) {
            echo print_r($text, 1) . PHP_EOL;
        }
    }

}
