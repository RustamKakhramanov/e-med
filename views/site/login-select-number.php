<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>

<div class="form-login-box">
    <form id="login-form" action="/site/login-operator" method="post">
        
        <?php if (isset($error_msg)) {?>
            <div class="alert alert-danger pt5 pb5">
                <?= $error_msg;?>
            </div>
        <?php }?>

        <div class="form-group">
            <label>Войти с номером</label>
            <select class="form-control selectpicker" name="LoginForm[number]">
                <option value=""><без номера></option>
                <?php foreach ($numbers as $number) { ?>
                    <option value="<?= $number; ?>"><?= $number; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <button type="submit" class="btn btn-default">Войти</button>
            </div>

        </div>
        <input type="hidden" name="LoginForm[username]" value="<?=$model->username;?>"/>
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
    </form>
</div>

<style>
    .body-login {
        background: #fff;
        position: relative;
    }

    .form-login-box {
        background: #416586;
        width: 270px;
        position: absolute;
        left: 50%;
        top: 45%;
        min-height: 130px;
        margin: -100px 0px 0px -135px;
        padding: 20px;
        color: #fff;
        border-radius: 5px;
    }

    .form-login-box label,
    .form-login-box .has-error .control-label {
        color: #fff;
    }

    .form-login-box .checkbox input[type="checkbox"]:checked + label::before,
    .form-login-box .checkbox input[type="radio"]:checked + label::before {
        background: #fff;
        border-color: #fff;
    }

    .form-login-box .checkbox label::after {
        color: #416586;
        text-align: left;
    }

    .form-login-box .btn {
        background: #416586;
        color: #fff;
        border-color: #fff;
    }

    .form-login-box .dropdown-menu {
        border: 1px solid rgb(255, 255, 255);
        border-top: none;
    }

    .form-login-box .btn-select {
        background: #fff;
        color: #000;
    }
</style>