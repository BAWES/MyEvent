<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "user_token".
 *
 * @property string $token_id
 * @property string $user_id
 * @property string $token_value
 * @property string $token_device
 * @property string $token_device_id
 * @property integer $token_status
 * @property string $token_last_used_datetime
 * @property string $token_expiry_datetime
 * @property string $token_created_datetime
 *
 * @property User $user
 */
class UserToken extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_EXPIRED = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_uuid', 'token_value', 'token_status'], 'required'],
            //[['user_uuid', 'token_status'], 'integer'],
            //[['token_last_used_datetime', 'token_expiry_datetime', 'token_created_datetime'], 'safe'],
            [['token_value', 'token_device', 'token_device_id'], 'string', 'max' => 255],
            //[['user_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['agent_id' => 'agent_id']],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'token_created_datetime',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'token_id' => 'Token ID',
            'user_uuid' => 'User UUID',
            'token_value' => 'Token Value',
            'token_device' => 'Token Device',
            'token_device_id' => 'Token Device ID',
            'token_status' => 'Token Status',
            'token_last_used_datetime' => 'Token Last Used Datetime',
            'token_expiry_datetime' => 'Token Expiry Datetime',
            'token_created_datetime' => 'Token Created Datetime',
        ];
    }

    /**
     * Generates unique access token to be used as value
     * @return string
     */
    public static function generateUniqueTokenString(){
        $randomString = Yii::$app->getSecurity()->generateRandomString();
        if(!static::findOne(['token_value' => $randomString ])){
            return $randomString;
        }else return static::generateUniqueTokenString();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_uuid' => 'user_uuid']);
    }
}
