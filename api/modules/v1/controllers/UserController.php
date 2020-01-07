<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\helpers\Url;
use yii\rest\Controller;
use \api\models\User;
        
/**
 * User Controller 
 */
class UserController extends Controller {

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

        // Bearer Auth checks for Authorize: Bearer <Token> header to login the user
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::className(),
        ];
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        $actions = parent::actions();
        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];
        return $actions;
    }

    /**
     * Return an overview of the users account details
     */
    public function actionDetails() {
        $user = User::find()
                ->where([
                    'user_uuid' => Yii::$app->user->identity->user_uuid
                ])
                ->one();

        if (!$user) {
            throw new \yii\web\HttpException(404, 'The requested User could not be found.');
        }

        // Return user access token if everything valid
        $accessToken = $user->accessToken->token_value;

        return [
            "token" => $accessToken,
            "user_uuid" => $user->user_uuid,
            "user_name" => $user->user_name,
            "user_email" => $user->user_email
        ];
    }

}
