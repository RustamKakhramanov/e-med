<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>

<div class="form-login-box">
    <form id="login-form" action="/login" method="post">
        
        <?php if ($error) {?>
            <div class="alert alert-danger pt5 pb5">
                <?= isset($error_msg) ? $error_msg : 'Ошибка! Неверные данные';?>
            </div>
        <?php }?>
        
        <div class="form-group">
            <label>Логин</label>
            <input type="text" class="form-control" name="LoginForm[username]" required=""/>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" class="form-control" name="LoginForm[password]" required=""/>
        </div>

        <div class="row">
            <div class="col-xs-4">
                <button type="submit" class="btn btn-default">Войти</button>
            </div>
            <div class="col-xs-8 text-right">
                <div class="checkbox" style="margin-top: -3px;">
                    <input id="loginform-rememberme" type="checkbox" name="LoginForm[rememberMe]" value="0" checked=""/>
                    <label for="loginform-rememberme">
                        Запомнить меня
                    </label>
                </div>
            </div>
        </div>
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
        min-height: 200px;
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

</style>