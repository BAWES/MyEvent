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
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Per-Page',
                    'X-Pagination-Total-Count'
                ],
            ],
        ];

        // Basic Auth accepts Base64 encoded username/password and decodes it for you
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'except' => ['options'],
            'auth' => function ($username, $password) {

                $candidate = Candidate::findByEmail($username);

                if ($candidate && $candidate->validatePassword($password)) {
                    return $candidate;
                }

                return null;
            }
        ];

        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        // also avoid for public actions like registration and password reset
        $behaviors['authenticator']['except'] = [
            'options',
            'request-reset-password',
            'update-password',
            'update-email',
            'signup',
            'login-by-google',
            'login-by-otp',
            'resend-verification-email',
            'verify-email',
            'is-email-verified',
            'locate'
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
     * Perform validation on the candidate account (check if he's allowed login to platform)
     * If everything is alright,
     * Returns the BEARER access token required for futher requests to the API
     * @return array
     */
    public function actionLogin() {
        $candidate = Yii::$app->user->identity;

        // Email and password are correct, check if his email has been verified
        // If email has been verified, then allow him to log in
        if ($candidate->email_verification != Candidate::EMAIL_VERIFIED) {
            $candidate->generateOtp();
            $candidate->save(false);

            return [
                "operation" => "error",
                "errorType" => "email-not-verified",
                "candidate_uuid" => $candidate->candidate_uuid,
                "message" => Yii::t('job', "Please click the verification link sent to you by email to activate your account"),
                "unVerifiedToken" => $this->_loginResponse($candidate)
            ];
        }

        //Update last active datetime for candidate
        $candidate->last_active_datetime = new \yii\db\Expression('NOW()');
        $candidate->save(false);

        return $this->_loginResponse($candidate);
    }

    /**
     * Sign up with google login
     */
    public function actionLoginByGoogle() {
        $token = Yii::$app->request->getBodyParam("idToken");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=" . $token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch));

        if (empty($response->email)) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Invalid Token')
            ];
        }

        $model = Candidate::find()->where([
                    'email' => $response->email
                ])->one();

        $newUser = 0;
        
        if (!$model) {
            
            $newUser = 1;
        
            $model = new Candidate;
            $model->scenario = "signup-google";

            $data = [
                'email' => $response->email,
                'firstname' => $response->given_name,
                'lastname' => (isset($response->family_name))?:$response->given_name,
                'gender' => $response->gender == 'male' ? 0 : 1,
                'is_password_set' => Candidate::NO_PASSWORD_SET,
                'email_verification' => Candidate::EMAIL_VERIFIED,
                'currency_pref' => Yii::$app->request->getBodyParam('currency_pref'),
                'language_pref' => Yii::$app->language
            ];

            $model->setAttributes($data);

            if (!$model->signup(false)) {
                if (isset($model->errors)) {
                    return [
                        "operation" => "error",
                        "message" => $model->errors,
                    ];
                } else {
                    return [
                        "operation" => "error",
                        "message" => Yii::t('job', "We've faced a problem creating your account, please contact us for assistance."),
                    ];
                }
            }
        }

        if ($response->picture && !$model->profile_photo)
            $model->setProfileByUrl(str_replace('s96', 's250', $response->picture));

        //Update last active datetime for candidate
        $model->last_active_datetime = new \yii\db\Expression('NOW()');
        $model->save();

        return $this->_loginResponse($model, $newUser);
    }

    /**
     * Sign up with otp
     */
    public function actionLoginByOtp() {
        $otp = Yii::$app->request->getBodyParam("otp");

        $model = Candidate::findByOtp($otp);

        if (!$model) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Invalid Token')
            ];
        }

        if(!$model->language_pref)
            $model->language_pref = Yii::$app->language;
                
        //remove otp
        $model->otp = null;

        //Update last active datetime for candidate
        $model->last_active_datetime = new \yii\db\Expression('NOW()');
        $model->save(false);

        return $this->_loginResponse($model);
    }

    /**
     * Check if candidate email already verified 
     */
    public function actionIsEmailVerified() {
        $token = Yii::$app->request->getBodyParam("token");

        $model = CandidateToken::find()
                ->where(['token_value' => $token])
                ->one();

        if (!$model || !$model->candidate) {
            return [
                'status' => 0
            ];
        }

        return [
            'status' => $model->candidate->new_email ? 0 : $model->candidate->email_verification
        ];
    }

    /**
     * Update candidate email address
     * @return type
     */
    public function actionUpdateEmail() {
        $unVerifiedToken = Yii::$app->request->getBodyParam("unVerifiedToken");
        $new_email = Yii::$app->request->getBodyParam("newEmail");

        $candidate = Candidate::findIdentityByUnVerifiedTokenToken($unVerifiedToken);

        if (!$candidate) {
            return [
                "operation" => "error",
                "message" => "Candidate not found"
            ];
        }

        if (!$new_email) {
            return [
                "operation" => "error",
                "message" => Yii::t('job', "Candidate new email address required")
            ];
        }

        if ($new_email == $candidate->email) {
            return [
                "operation" => "error",
                "message" => Yii::t('job', "Candidate new email address is same as old email")
            ];
        }

        /**
         * Opt will expiry after 60 minutes, so user have to login back to update 
         * email 
         */
        if (!$candidate->findByOtp($candidate->otp, 60)) {
            return [
                "operation" => "error-session-expired",
                "message" => Yii::t('employer', "Session expired, please log back in")
            ];
        }
        
        $candidate->scenario = "updateEmail";

        $candidate->new_email = $new_email;

        if ($candidate->save()) { 

            //extend otp to fix: https://www.pivotaltracker.com/story/show/169037267
            
            $candidate->generateOtp();
            
            //to verify new email address 
            
            $candidate->sendVerificationEmail();

            return [
                "operation" => "success",
                "message" => Yii::t('job', "Candidate Account Info Updated Successfully, please check email to verify new email address"),
                "unVerifiedToken" => $this->_loginResponse($candidate)
            ];
        } else {
            return [
                "operation" => "error",
                "message" => $candidate->errors
            ];
        }  
    }

    /**
     * Sends password reset email to user
     * @return array
     */
    public function actionRequestResetPassword() {
        
        $emailInput = Yii::$app->request->getBodyParam("email");

        $model = new \candidate\models\PasswordResetRequestForm();
        $model->email = $emailInput;

        $errors = false;

        if ($model->validate()) {

            $candidate = Candidate::findOne([
                'email' => $model->email,
            ]);

            if ($candidate) {

                //Check if this user sent an email in past few minutes (to limit email spam)
                $emailLimitDatetime = new \DateTime($candidate->limit_email);
                date_add($emailLimitDatetime, date_interval_create_from_date_string('1 minutes'));
                $currentDatetime = new \DateTime('now');

                if ($candidate->limit_email && $currentDatetime < $emailLimitDatetime) {
                    $difference = $currentDatetime->diff($emailLimitDatetime);
                    $minuteDifference = (int) $difference->i;
                    $secondDifference = (int) $difference->s;

                    $errors = Yii::t('job', "Email was sent previously, you may request another one in {numMinutes, number} minutes and {numSeconds, number} seconds", [
                                'numMinutes' => $minuteDifference,
                                'numSeconds' => $secondDifference,
                    ]);
                } else if (!$model->sendEmail($candidate)) {
                    $errors = Yii::t('job', 'Sorry, we are unable to reset a password for email provided.');
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
            'message' => Yii::t('job', 'Please check the link sent to you on your email to set new password.')
        ];
    }

    /**
     * Updates password based on passed token
     * @return array
     */
    public function actionUpdatePassword() {
        $token = Yii::$app->request->getBodyParam("token");
        $newPassword = Yii::$app->request->getBodyParam("newPassword");
        $cPassword = Yii::$app->request->getBodyParam("cPassword");

        $candidate = Candidate::findByPasswordResetToken($token);

        if (!$candidate) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Invalid password reset token. Please request another password reset email')
            ];
        }

        if (!$newPassword) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Password field required')
            ];
        }

        if (!$cPassword) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Confirm Password field required')
            ];
        }

        if ($cPassword != $newPassword) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Password & Confirm Password does not match')
            ];
        }

        $candidate->setPassword($newPassword);
        $candidate->removePasswordResetToken();
        $candidate->is_password_set = Candidate::PASSWORD_SET;

        //Update last active datetime for candidate
        $candidate->last_active_datetime = new \yii\db\Expression('NOW()');
        $candidate->save(false);

        //Whenever a user changes his password using any method (password reset email / profile page), we need to send out the following email to confirm that his password was set
        
        \Yii::$app->mailer->htmlLayout = "layouts/text";

        \Yii::$app->mailer->compose([
                'html' => 'candidate/password-reset-confirmed'
            ])
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['appName']])
            ->setTo($candidate->email)
            ->setSubject(Yii::t('job', 'Your password reset was a success'))
            ->send();

        return $this->_loginResponse($candidate);
    }

    /**
     * Signup by candidate, only firstname, lastname, email and password needed
     * @return array
     */
    public function actionSignup() {
        
        $model = new User();
        $model->scenario = "signup";

        $data = [
            'user_name' => Yii::$app->request->getBodyParam('username'),
            'user_email' => Yii::$app->request->getBodyParam('email'),
            'user_password_hash' => Yii::$app->request->getBodyParam('password'),
        ];

        $model->setAttributes($data);
         Yii::error('$model->' . json_encode($model));

        if (!$model->signup()) {
            Yii::error('enter !$model->signup()');
            if (isset($model->errors)) {
                return [
                    "operation" => "error",
                    "message" => $model->errors,
                ];
            } else {
                return [
                    "operation" => "error",
                    "message" => Yii::t('job', "We've faced a problem creating your account, please contact us for assistance.")
                ];
            }
        }

         Yii::error('pass !$model->signup()');
         
        return [
            "operation" => "success",
            "user_uuid" => $model->user_uuid,
            "message" =>  "Please click on the link sent to you by email to verify your account",
            "unVerifiedToken" => $this->_loginResponse($model)
        ];
    }

    /**
     * Re-send manual verification email to candidate
     * @return array
     */
    public function actionResendVerificationEmail() {
        $emailInput = Yii::$app->request->getBodyParam("email");

        $candidate = Candidate::findOne([
                    'email' => $emailInput,
        ]);

        $errors = false;
        $errorCode = null; //error code

        if ($candidate) {
            if ($candidate->email_verification == Candidate::EMAIL_VERIFIED) {
                return [
                    'operation' => 'error',
                    'errorCode' => 1,
                    'message' => Yii::t('job', 'You have verified your email')
                ];
            }

            //Check if this user sent an email in past few minutes (to limit email spam)
            $emailLimitDatetime = new \DateTime($candidate->limit_email);
            date_add($emailLimitDatetime, date_interval_create_from_date_string('1 minutes'));
            $currentDatetime = new \DateTime();

            if ($candidate->limit_email && $currentDatetime < $emailLimitDatetime) {
                $difference = $currentDatetime->diff($emailLimitDatetime);
                $minuteDifference = (int) $difference->i;
                $secondDifference = (int) $difference->s;

                $errorCode = 2;

                $errors = Yii::t('job', "Email was sent previously, you may request another one in {numMinutes, number} minutes and {numSeconds, number} seconds", [
                            'numMinutes' => $minuteDifference,
                            'numSeconds' => $secondDifference,
                ]);
            } else if ($candidate->email_verification == Candidate::EMAIL_NOT_VERIFIED) {
                $candidate->sendVerificationEmail();
            }
        } else {
            $errorCode = 3;
            $errors['email'] = [Yii::t('job', 'Candidate Account not found')];
        }

        // If errors exist show them

        if ($errors) {
            return [
                'errorCode' => $errorCode,
                'operation' => 'error',
                'message' => $errors
            ];
        }

        // Otherwise return success
        return [
            'operation' => 'success',
            'message' => Yii::t('job', 'Please click on the link sent to you by email to verify your account'),
        ];
    }

    /**
     * Process email verification
     * @return array
     */
    public function actionVerifyEmail() {
        $code = Yii::$app->request->getBodyParam("code");
        $email = Yii::$app->request->getBodyParam("email");
        
        //check limit reached

        $totalInvalidAttempts = CandidateEmailVerifyAttempt::find()
                ->where([
                    'email' => $email,
                    'ip_address' => Yii::$app->getRequest()->getUserIP()
                ])
                ->andWhere(new \yii\db\Expression("created_at >= DATE_SUB(NOW(),INTERVAL 1 HOUR)"))//last 1 hour 
                ->count();

        if ($totalInvalidAttempts > 4) {
            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'You reached your limit to verify email. Please try again after an hour.')
            ];
        }

        $candidate = Candidate::verifyEmail($code);

        if ($candidate) {
            //remove old email verification attempts

            CandidateEmailVerifyAttempt::deleteAll([
                'email' => $email,
                'ip_address' => Yii::$app->getRequest()->getUserIP()
            ]);

            //remove otp

            $candidate->otp = null;
            $candidate->save(false);

            return $this->_loginResponse($candidate);
        } else {
            //add entry for invalid attempt

            $model = new CandidateEmailVerifyAttempt;
            $model->code = $code;
            $model->email = $email;
            $model->ip_address = Yii::$app->getRequest()->getUserIP();
            $model->save();

            return [
                'operation' => 'error',
                'message' => Yii::t('job', 'Invalid email verification code.')
            ];
        }
    }


    /**
     * Return candidate data after successful login
     * @param type $user
     * @return type
     */
    private function _loginResponse($user, $new_user = 0) {
        
        // Return Candidate access token if everything valid
            
        $accessToken = $user->accessToken->token_value;

        return [
            "operation" => "success",
            "token" => $accessToken,
            "id" => $user->user_uuid,
            "name" => $user->user_name,
            "email" => $user->user_email,
            "user_created_at" => $user->user_created_at
        ];
    }
}
