<?php
namespace api\models;

use Yii;
use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'targetAttribute' => 'user_email',
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @param common\models\User $user
     * @return boolean whether the email was sent
     */
    public function sendEmail($user = null)
    {
        if(!$user){
            $user = User::findOne([
                'user_email' => $this->user_email,
            ]);
        }

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->user_password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            //Update user last email limit timestamp
            $user->user_limit_email = new \yii\db\Expression('NOW()');

            if ($user->save(false)) {
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/update-password', 'token' => $user->user_password_reset_token, 'password' => ]);

                //Send English Email
                return \Yii::$app->mailer->compose([
                    'html' => 'passwordResetToken-html',
                    'text' => 'passwordResetToken-text'
                ], [
                    'user' => $user,
                    'resetLink' => $resetLink
                ])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name ])
                ->setTo($user->user_email)
                ->setSubject('[MyEvent] Password Reset')
                ->send();

            }
        }

        return false;
    }
}
