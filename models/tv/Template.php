<?php

namespace app\models\tv;

class Template {

    public function getWidgets() {
        $list = [];
        foreach ($this->_usedWidgets as $widgetName) {
            $class = '\app\models\tv\widget\\' . ucfirst($widgetName);
            $list[$widgetName] = new $class();
            //todo load attr
        }

        return $list;
    }

}
