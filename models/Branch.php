<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "branch".
 *
 * @property integer $id
 * @property string $name
 * @property string $api_key
 * @property boolean $deleted
 * @property string $extra
 * @property bool $kassa Использовать Webkassa
 * @property string $kassa_mode Режим Webkassa
 * @property string $kassa_url
 * @property string $kassa_login
 * @property string $kassa_password
 * @property string $kassa_token
 *
 * @property Check[] $checks
 * @property Contract[] $contracts
 * @property Contractor[] $contractors
 * @property DestTemplate[] $destTemplates
 * @property Device[] $devices
 * @property Direction[] $directions
 * @property Doctor[] $doctors
 * @property Event[] $events
 * @property Patients[] $patients
 * @property Price[] $prices
 * @property PriceGroup[] $priceGroups
 * @property Reception[] $receptions
 * @property Shift[] $shifts
 * @property Speciality[] $specialities
 * @property Subdivision[] $subdivisions
 * @property Template[] $templates
 * @property User[] $users
 */
class Branch extends \yii\db\ActiveRecord {

    const
        KASSA_MODE_SINGLE = 'single',
        KASSA_MODE_MANY = 'many';

    protected $_kassaModeLabels = [
        self::KASSA_MODE_MANY => 'Несколько касс',
        self::KASSA_MODE_SINGLE => 'Одна касса',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'branch';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['deleted', 'kassa'], 'boolean'],
            [['extra'], 'string'],
            [['name', 'api_key', 'kassa_mode', 'kassa_url', 'kassa_login', 'kassa_password', 'kassa_token'], 'string', 'max' => 255],
            ['kassa_url', 'default', 'value' => 'https://devkkm.webkassa.kz/api/'],
            [['kassa_mode', 'kassa_url', 'kassa_login', 'kassa_password', 'kassa_token'], 'kassaFieldsValidate', 'skipOnEmpty' => false]
        ];
    }

    public function kassaFieldsValidate($attr, $field) {
        if ($this->kassa) {
            if (!$this->$attr) {
                $this->addError($attr, 'Требуется заполнить');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'deleted' => 'Deleted',
            'api_key' => 'Api Key',
            'extra' => 'Extra',
            'kassa' => 'Использовать Webkassa',
            'kassa_mode' => 'Режим Webkassa',
            'kassa_url' => 'Адрес',
            'kassa_login' => 'Логин',
            'kassa_password' => 'Пароль',
            'kassa_token' => 'Токен',
        ];
    }

    function setDefault() {
        $this->deleted = false;
        $this->extra = '[]';
        $this->api_key = Yii::$app->security->generateRandomString(20);
        $this->kassa_url = 'https://devkkm.webkassa.kz/api/';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecks() {
        return $this->hasMany(Check::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts() {
        return $this->hasMany(Contract::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractors() {
        return $this->hasMany(Contractor::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestTemplates() {
        return $this->hasMany(DestTemplate::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices() {
        return $this->hasMany(Device::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirections() {
        return $this->hasMany(Direction::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctors() {
        return $this->hasMany(Doctor::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents() {
        return $this->hasMany(Event::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatients() {
        return $this->hasMany(Patients::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrices() {
        return $this->hasMany(Price::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceGroups() {
        return $this->hasMany(PriceGroup::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptions() {
        return $this->hasMany(Reception::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShifts() {
        return $this->hasMany(Shift::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialities() {
        return $this->hasMany(Speciality::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivisions() {
        return $this->hasMany(Subdivision::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplates() {
        return $this->hasMany(Template::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['branch_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtensions() {
        return $this->hasMany(Extensions::className(), ['branch_id' => 'id'])->orderBy('name');
    }

    public function delete() {
        $this->deleted = true;
        $this->save(false);
        //parent::delete();
    }

    public function getExtraParam($name) {
        $params = json_decode($this->extra, true);
        return isset($params[$name]) ? $params[$name] : null;
    }

    public function getKassaModeLabels() {
        return $this->_kassaModeLabels;
    }

    public function getKassaModeLabel() {
        return $this->_kassaModeLabels[$this->_kassaModeLabels];
    }

}
