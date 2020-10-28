<?php

namespace app\models\tv\template;

use app\models\tv\Template;

class Basic extends Template {

    protected $_usedWidgets = ['schedule', 'oncall', 'discharged'];

}
