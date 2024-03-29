<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $user_uuid
 * @property string $user_name
 * @property string $user_email
 * @property string $user_auth_key
 * @property string $user_password_hash
 * @property string $user_password_reset_token
 * @property int $user_status
 * @property int $user_email_verified
 * @property int $user_limit_email
 * @property int $user_created_at
 * @property int $user_updated_at
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface {

    /**
     * Field for temporary password. If set, it will overwrite the old password on save
     * @var string
     */
    public $tempPassword;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const EMAIL_NOT_VERIFIED = 0;
    const EMAIL_VERIFIED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_uuid'], 'string', 'max' => 36],
            [['user_uuid'], 'unique'],
            [['user_name', 'user_email'], 'required'],
            [['tempPassword'], 'required', 'on' => 'create'],
            [['tempPassword'], 'safe'],
            [['user_status'], 'integer'],
            [['user_name', 'user_email', 'user_password_reset_token'], 'string', 'max' => 255],
            [['user_email'], 'email'],
            [['user_email'], 'unique'],
            [['user_password_reset_token'], 'unique'],
        ];
    }

    /**
     * Scenarios for validation and massive assignment
     */
    public function scenarios() {
        $scenarios = parent::scenarios();

        $scenarios['signup'] = ['user_email', 'tempPassword', 'user_name', 'user_email'];

        return $scenarios;
    }

    /**
     * 
     * @return type
     */
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'user_uuid',
                ],
                'value' => function() {
                    if (!$this->user_uuid)
                        $this->user_uuid = Yii::$app->db->createCommand('SELECT uuid()')->queryScalar();

                    return $this->user_uuid;
                }
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'user_created_at',
                'updatedAtAttribute' => 'user_updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_uuid' => 'User ID',
            'user_name' => 'Full Name',
            'user_email' => 'Email',
            'user_auth_key' => 'Auth Key',
            'user_password_hash' => 'Password',
            'user_password_reset_token' => 'Password Reset Token',
            'user_status' => 'Status',
            'user_email_verified' => 'Email Verified',
            'user_limit_email' => 'Limit Email',
            'user_created_at' => 'Created At',
            'user_updated_at' => 'Updated At',
            'tempPassword' => 'Password'
        ];
    }

    /**
     * Returns String value of current status
     * @return string
     */
    public function getStatus() {
        switch ($this->user_status) {
            case self::STATUS_ACTIVE:
                return "Active";
                break;
            case self::STATUS_DELETED:
                return "Deleted";
                break;
        }

        return "Couldnt find a status";
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            // Generate Auth key if its a new user record
            if ($insert) {
                $this->generateAuthKey();
            }

            // If tempPassword is set, save it as the new password for this user
            if ($this->tempPassword) {
                $this->setPassword($this->tempPassword);
            }

            return true;
        }
    }

    /**
     * Signs user up.
     * @return static|null the saved model or null if saving fails
     */
    public function signup() {
        $oldPasswordInput = $this->user_password_hash;

        $this->setPassword($this->user_password_hash);
        $this->generateAuthKey();

        if ($this->save()) {
            $this->sendVerificationEmail();

            //Log user signup
//            Yii::info("[New User Signup Manual] ".$this->user_email, __METHOD__);

            return $this;
        } else {
            //Reset password to hide encrypted value
            $this->user_password_hash = $oldPasswordInput;
        }

        return null;
    }

    /**
     * Sends an email requesting a user to verify his email address
     * @return boolean whether the email was sent
     */
    public function sendVerificationEmail() {

        //Update user last email limit timestamp
        $this->user_limit_email = new Expression('NOW()');
        $this->save(false);

        // Generate Reset Link
        $verificationUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'auth/verify-email',
            'code' => $this->user_auth_key,
            'verify' => $this->user_uuid
        ]);


        return Yii::$app->mailer->compose([
                            'html' => 'emailVerify-html',
                            'text' => 'emailVerify-text',
                                ], [
                            'user' => $this,
                            'verificationUrl' => $verificationUrl
                        ])
                        ->setFrom([\Yii::$app->params['supportEmail']])
                        ->setTo($this->user_email)
                        ->setSubject('Please confirm your email address')
                        ->send();
    }

    /**
     * Verify user
     */
    public function verifyUser() {
        $this->user_email_verified = User::EMAIL_VERIFIED;
        $this->save(false);
    }

    /**
     * Create an Access Token Record for this User
     * if the user already has one, it will return it instead
     * @return \common\models\UserToken
     */
    public function getAccessToken() {
        // Return existing inactive token if found
        $token = UserToken::findOne([
                    'user_uuid' => $this->user_uuid,
                    'token_status' => UserToken::STATUS_ACTIVE
        ]);
        
        if ($token) {
            return $token;
        }

        // Create new inactive token
        $token = new UserToken();
        $token->user_uuid = $this->user_uuid;
        $token->token_value = UserToken::generateUniqueTokenString();
        $token->token_status = UserToken::STATUS_ACTIVE;
        $token->save(false);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return static::findOne(['user_uuid' => $id, 'user_status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $token = UserToken::find()->where(['token_value' => $token])->with('user')->one();
        if ($token) {
            return $token->user;
        }
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['user_email' => $email, 'user_status' => self::STATUS_ACTIVE]);
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
                    'user_password_reset_token' => $token,
                    'user_status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->user_auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->user_password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->user_password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->user_auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->user_password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->user_password_reset_token = null;
    }

}
