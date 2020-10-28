<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface {

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_MODIRATION = 2;
    const ROLE_ROOT = 1;
    const ROLE_ADMIN = 10;
    const ROLE_SPECIALIST = 11;
    const ROLE_OPERATOR = 12;
    const ROLE_MANAGER = 13;
    const ROLE_CASHIER = 14;
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static $arStatusLabels = [
        self::STATUS_DELETED => 'не активен',
        self::STATUS_ACTIVE => 'активен',
    ];
    public static $arRoleNames = [
        self::ROLE_ROOT => 'root',
        self::ROLE_SPECIALIST => 'specialist',
        self::ROLE_ADMIN => 'admin',
        self::ROLE_OPERATOR => 'operator',
        self::ROLE_MANAGER => 'manager',
        self::ROLE_CASHIER => 'cashier',
    ];
    public static $arRoleLabels = [
        self::ROLE_ROOT => 'суперадмин',
        self::ROLE_SPECIALIST => 'специалист',
        self::ROLE_ADMIN => 'администратор',
        self::ROLE_OPERATOR => 'оператор',
        self::ROLE_MANAGER => 'управляющий',
        self::ROLE_CASHIER => 'кассир',
    ];
    public $password_new;
    public static $menu = [
        self::ROLE_ROOT => ['branch', 'users'],
        self::ROLE_SPECIALIST => ['dashboard', 'patient', 'template', 'dest-template', 'reception'],
        self::ROLE_ADMIN => ['patient', 'speciality', 'subdivision', 'doctor', 'schedule', 'event', 'price', 'direction', 'template', 'contractor', 'contract', 'check', 'report', 'report-queue', 'report-operator', 'report-operator-detail', 'report-operator-summary', 'users', 'dest-template', 'tv', 'cashbox'],
        self::ROLE_OPERATOR => ['schedule', 'event', 'price', 'report-queue', 'report-operator'],
        self::ROLE_MANAGER => ['schedule', 'report-queue', 'report-operator-detail', 'report-operator-summary'],
        self::ROLE_CASHIER => ['cashier', 'cashier-checks', 'cashier-report'],
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {

        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {

        return [
            ['status_id', 'default', 'value' => self::STATUS_ACTIVE],
            ['status_id', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['status_id', 'role_id', 'fio'], 'safe'],
            [['extra'], 'string'],
            //username
            ['username', 'unique'],
            //password
            [['password_new'], 'required', 'on' => self::SCENARIO_CREATE, 'message' => 'Обязательно'],
            [['password_new'], 'string', 'min' => 6],
            [['doctor_id', 'branch_id'], 'integer'],
            [['doctor_id'], 'doctorValidate'],
            [['username', 'role_id'], 'required', 'message' => 'Обязательно'],
            //secure
            //[['username'], 'filter', 'filter'=>'strip_tags'],
            [['password_new'], 'default', 'on' => self::SCENARIO_UPDATE]
        ];
    }

    //todo сделать валидацию доктора и фио при разных сценариях
    public function doctorValidate($field, $attribute) {

        if ($this->role_id == self::ROLE_SPECIALIST) {

            if (!$this->$field) {
                $this->addError($field, 'Требуется выбрать специалиста');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password_new' => 'Пароль',
            'status_id' => 'Статус',
            'role_id' => 'Роль',
            'created_at' => 'Дата регистрации',
            'updated_at' => 'Дата обновления',
            //custom
            'fio' => 'ФИО',
            'doctor_id' => 'Специалист',
            'branch_id' => 'Филиал'
        ];
    }

    public function setDefault() {
        $this->extra = '[]';
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            $this->updated_at = date('Y-m-d H:i:s');

            if (!$this->id) {
                $this->created_at = date('Y-m-d H:i:s');
            }

            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        //return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);

        return self::find()
                        //->with('info')
                        ->where(['id' => $id, 'status_id' => self::STATUS_ACTIVE])
                        ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);

        return static::find()
                        ->where(['status_id' => self::STATUS_ACTIVE])
                        ->andWhere(['username' => $username])
                        ->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status_id' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function getRoleName() {
        return self::$arRoleNames[$this->role_id];
    }

    public function getRoleNameRu() {
        return self::$arRoleLabels[$this->role_id];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor() {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * инициалы
     * @return string
     */
    public function getInitials() {

        if ($this->doctor_id) {

            return $this->doctor->initials;
        } else {
            $names = explode(' ', $this->fio);
            if (count($names)) {
                if (isset($names[1])) {
                    $names[1] = mb_strtoupper(mb_substr($names[1], 0, 1)) . '.';
                }

                if (isset($names[2])) {
                    $names[2] = mb_strtoupper(mb_substr($names[2], 0, 1)) . '.';
                }

                return implode(' ', $names);
            }

            return '';
        }
    }

    /**
     * получить устройство (фиск) для пользователя
     * @return type
     */
    public function getCurrentDevice() {
        //todo
        return Device::find()->where(['deleted' => false])->one();
    }

//    public function isRoot(){
//        $root = false;
//        foreach (Yii::$app->authManager->getRolesByUser($this->id) as $role => $obj) {
//            if ($role == 'root') {
//                $root = true;
//                break;
//            }
//        }
//        
//        return $root;
//    }

    public function getIsRoot() {
        return $this->role_id == self::ROLE_ROOT;
    }

    public function getExtraParam($name) {
        $params = json_decode($this->extra, true);
        return isset($params[$name]) ? $params[$name] : null;
    }
    
    /**
     * получить внутренние номера астера
     * @return array
     */
    public function getAsterNumbers(){
        $data = [];
        $temp = explode(';', $this->getExtraParam('number'));
        foreach ($temp as $item) {
            $item = trim($item);
            if ($item != '') {
                $data[] = $item;
            }
        }
        
        return $data;
    }
    
    /**
     * @param string $number - под каким номером зашел
     * создать сессию для оператора
     * @return integer
     */
    public function createSession($number = null){
        $sess = new UserSession();
        $sess->setAttributes([
            'user_id' => $this->id,
            'number' => $number
        ]);
        $sess->save();
        
        Yii::$app->session->set('usess', [
            'number' => $number,
            'sess_id' => $sess->id
        ]);
        
        return $sess->id;
    }

    public function getCashbox() {
        if ($this->getExtraParam('cashbox_id')) {
            return Cashbox::findOne([
                'id' => $this->getExtraParam('cashbox_id')
            ]);
        }
    }

}
