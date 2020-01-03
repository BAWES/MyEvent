<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use common\models\User;

/**
 * Auth controller provides the initial access token that is required for further requests
 * It initially authorizes via Http Basic Auth using a base64 encoded username and password
 */
class AuthController extends Controller {

    public function behaviors() {
        $behaviors = parent::behaviors();

        // remove authentication filter for cors to work
        unset($behaviors['authenticator']);

        // Allow XHR Requests from our different subdomains and dev machines
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => Yii::$app->params['allowedOrigins'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ],
        ];

        // Basic Auth accepts Base64 encoded username/password and decodes it for you
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'except' => ['options'],
            'auth' => function ($email, $password) {
                $user = User::findByEmail($email);
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }

                return null;
            }
        ];
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        // also avoid for public actions like registration and password reset
        $behaviors['authenticator']['except'] = [
            'options',
            'verify-email',
            'validate',
            'update-password',
            'create-account',
            'request-reset-password',
            'resend-verification-email'
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        $actions = parent::actions();

        // Return Header explaining what options are available for next request
        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];
        return $actions;
    }

    /**
     * Perform validation on the user account (check if he's allowed login to platform)
     * If everything is alright,
     * Returns the BEARER access token required for futher requests to the API
     * @return array
     */
    public function actionLogin() {
        $user = Yii::$app->user->identity;

        // Email and password are correct, check if his email has been verified
        // If user email has been verified, then allow him to log in
        if ($user->user_email_verified != User::EMAIL_VERIFIED) {
            return [
                "operation" => "error",
                "errorType" => "email-not-verified",
                "message" => "Please click the verification link sent to you by email to activate your account",
            ];
        }

        // Return user access token if everything valid
        $accessToken = $user->accessToken->token_value;
        
        return [
            "operation" => "success",
            "token" => $accessToken,
            "userId" => $user->user_uuid,
            "name" => $user->user_name,
            "email" => $user->user_email
        ];
    }

    /**
     * Creates new user account manually
     * @return array
     */
    public function actionCreateAccount() {
        $model = new User();
        $model->scenario = "signup";

        $model->user_name = Yii::$app->request->getBodyParam("username");
        $model->user_email = Yii::$app->request->getBodyParam("email");
        $model->user_password_hash = Yii::$app->request->getBodyParam("password");

        if (!$model->signup()) {
            if (isset($model->errors['user_email'])) {
                return [
                    "operation" => "error",
                    "message" => $model->errors['user_email']
                ];
            } else {
                return [
                    "operation" => "error",
                    "message" => "We've faced a problem creating your account, please contact us for assistance."
                ];
            }
        }

        return [
            "operation" => "success",
            "message" => "Please click on the link sent to you by email to verify your account"
        ];
    }

    /**
     * Re-send manual verification email to user
     * @return array
     */
    public function actionResendVerificationEmail() {
        $emailInput = Yii::$app->request->getBodyParam("email");

        $user = User::findOne([
                    'user_email' => $emailInput,
        ]);

        $errors = false;

        if ($user) {
            //Check if this user sent an email in past few minutes (to limit email spam)
            $emailLimitDatetime = new \DateTime($user->user_limit_email);
            date_add($emailLimitDatetime, date_interval_create_from_date_string('2 minutes'));
            $currentDatetime = new \DateTime();

            if ($currentDatetime < $emailLimitDatetime) {
                $difference = $currentDatetime->diff($emailLimitDatetime);
                $minuteDifference = (int) $difference->i;
                $secondDifference = (int) $difference->s;

                $errors = "Email was sent previously, you may request another one in " . $minuteDifference . "minutes and " . $secondDifference . " seconds";
            } else if ($user->user_email_verified == User::EMAIL_NOT_VERIFIED) {
                $user->sendVerificationEmail();
            }
        }

        // If errors exist show them
        if ($errors) {
            return [
                'operation' => 'error',
                'message' => $errors
            ];
        }

        // Otherwise return success
        return [
            'operation' => 'success',
            'message' => 'Please click on the link sent to you by email to verify your account'
        ];
    }

    /**
     * Process email verification
     * @return array
     */
    public function actionVerifyEmail() {
        
        $code = Yii::$app->request->getBodyParam("code");
        $verify = Yii::$app->request->getBodyParam("verify");

        
        //Code is his auth key, check if code is valid
        $user = User::findOne(['user_auth_key' => $code]);
        
        if ($user) {
            //If not verified
            if ($user->user_email_verified == User::EMAIL_NOT_VERIFIED) {
                //Verify this users  email
                $user->user_email_verified = User::EMAIL_VERIFIED;
                $user->save(false);
            }

            return [
                'operation' => 'success',
                'message' => 'You have verified your email'
            ];
        }

        //inserted code is invalid
        return [
            'operation' => $code,
            'message' => 'Invalid email verification code. Account might already be activated. Please try to login again.'
        ];
    }

    /**
     * Sends password reset email to user
     * @return array
     */
    public function actionRequestResetPassword() {
        $emailInput = Yii::$app->request->getBodyParam("email");

        $model = new \api\models\PasswordResetRequestForm();
        $model->email = $emailInput;

        $errors = false;

        if ($model->validate()) {

            $user = User::findOne([
                        'user_email' => $model->email,
            ]);

            if ($user) {
                //Check if this user sent an email in past few minutes (to limit email spam)
                $emailLimitDatetime = new \DateTime($user->user_limit_email);
                date_add($emailLimitDatetime, date_interval_create_from_date_string('2 minutes'));
                $currentDatetime = new \DateTime();

                if ($currentDatetime < $emailLimitDatetime) {
                    $difference = $currentDatetime->diff($emailLimitDatetime);
                    $minuteDifference = (int) $difference->i;
                    $secondDifference = (int) $difference->s;

                    $errors = "Email was sent previously, you may request another one in " . $minuteDifference . " minutes and " . $secondDifference .  " seconds";
                   
                } elseif (!$model->sendEmail($user)) {
                    $errors = 'Sorry, we are unable to reset password for email provided.';
                }
            }
        } else if (isset($model->errors['email'])) {
            $errors = $model->errors['email'];
        }

        // If errors exist show them
        if ($errors) {
            return [
                'operation' => 'error',
                'message' => $errors
            ];
        }

        // Otherwise return success
        return [
            'operation' => 'success',
            'message' => 'Password reset link sent, please check your email for further instructions.'
        ];
    }

    /**
     * Updates password based on passed token
     * @return array
     */
    public function actionUpdatePassword() {
         $token = Yii::$app->request->getBodyParam("token");
        $newPassword = Yii::$app->request->getBodyParam("newPassword");

        $user = User::findByPasswordResetToken($token);
        if (!$user || !$newPassword) {
            return [
                'operation' => 'error',
                'message' => 'Invalid password reset token. Please request another password reset email.'
            ];
        }

        $user->setPassword($newPassword);
        $user->removePasswordResetToken();
        $user->save(false);

        return [
            'operation' => 'success',
            'message' => 'Your password has been reset.'
        ];
    }
}
