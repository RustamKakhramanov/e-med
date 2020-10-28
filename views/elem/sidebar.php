<?php

use yii\widgets\Menu;
use yii\base\Model;

$userAllowKeys = \app\models\User::$menu[Yii::$app->user->identity->role_id];
$widgetItems = [];
$current = '/' . Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

foreach (\app\models\Menu::$items as $key => $item) {
    if (in_array($key, $userAllowKeys)) {
        $active = false;
        if (isset($item['link'])) {
            if ($item['link'] == $current) {
                $active = true;
            }
        } else {
            if (Yii::$app->controller->id == $key) {
                $active = true;
            }
        }

        $widgetItems[] = [
            //'label' => '<span class="ico-' . $item['icon'] . '"></span><span class="text"><span>' . $item['title'] . '</span></span>',
            'label' => '<table><tr><td class="td-icon"><span class="ico-' . $item['icon'] . '"></span></td><td class="td-text">' . $item['title'] . '</td></tr></table>',
            'url' => isset($item['link']) ? $item['link'] : '/' . $key,
            'active' => $active,
        ];
    }
}
?>
<div class="inner">
    <a href="/">
        <div class="sidebar-logo" title="<?= @Yii::$app->user->identity->branch->name; ?>"></div>
    </a>

    <ul class="sidebar-user sidebar-user-large">
        <li class="info dropdown">
            <a class="dropdown-handler" href="#" data-toggle="dropdown"><?= Yii::$app->user->getIdentity()->username; ?>
                <span class="sidebar-caret"></span><span
                        class="sidebar-user-role"><?= Yii::$app->user->getIdentity()->roleNameRu; ?></span></a>
            <ul class="dropdown-menu">
                <li><a href="#">Профиль</a></li>
                <li><a href="/logout">Выйти</a></li>
            </ul>
        </li>
    </ul>

    <?=
    Menu::widget([
        'encodeLabels' => false,
        'linkTemplate' => '<a href="{url}" class="clearfix">{label}</a>',
        'options' => [
            'class' => 'sidebar-nav clearfix',
        ],
        'items' => $widgetItems
    ])
    ?>

</div>