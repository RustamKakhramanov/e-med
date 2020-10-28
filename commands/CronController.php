<?php

namespace app\commands;

use yii\console\Controller;
use PHPMailer;

class CronController extends Controller {

    public function actionIndex() {
        
    }

    public function actionAwsCheck() {
        $status = exec('service aws status');
        if ($status == 'aws stop/waiting') {

            $mails = array(
                'top60@yandex.ru',
                'maximg@cloudsystem.kz'
            );

            $text = '<p>Дата: ' . date('H:i d.m.Y ') . '</p>';

            $Mail = new PHPMailer;
            $Mail->isSMTP();
            $Mail->CharSet = 'UTF-8';
            $Mail->Port = 465;
            $Mail->Host = 'smtp.yandex.ru';
            $Mail->SMTPAuth = true;
            $Mail->Username = 'noreply@nettop.kz';
            $Mail->Password = '123123';
            $Mail->SMTPSecure = 'ssl';
            $Mail->From = 'noreply@nettop.kz';
            $Mail->FromName = 'noreply';

            foreach ($mails as $email) {
                $Mail->addAddress($email);
            }
            $Mail->isHTML(true);
            $Mail->Subject = 'EMED_LOG';
            $Mail->Body = $text;

            $Mail->send();
            
            exec('service aws start');
        }
    }

}
