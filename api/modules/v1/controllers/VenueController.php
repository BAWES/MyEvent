<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use api\models\User;
use common\models\Venue;

/**
 * Venue controller 
 */
class VenueController extends Controller {

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
     * 
     */
    public function actionCreateVenue() {
        $model = new Venue();

        $model->user_uuid = Yii::$app->user->identity->user_uuid;
        $model->venue_name = Yii::$app->request->getBodyParam("venue_name");
        $model->venue_location = Yii::$app->request->getBodyParam("venue_location");
        $model->venue_location_latitude = Yii::$app->request->getBodyParam("venue_location_latitude");
        $model->venue_location_longitude = Yii::$app->request->getBodyParam("venue_location_longitude");
        $model->venue_description = Yii::$app->request->getBodyParam("venue_description");
        $model->venue_contact_email = Yii::$app->request->getBodyParam("venue_contact_email");
        $model->venue_contact_phone = Yii::$app->request->getBodyParam("venue_contact_phone");
        $model->venue_contact_website = Yii::$app->request->getBodyParam("venue_contact_website");
        $model->venue_capacity_minimum = Yii::$app->request->getBodyParam("venue_capacity_minimum");
        $model->venue_capacity_maximum = Yii::$app->request->getBodyParam("venue_capacity_maximum");
        $model->venue_operating_hours = Yii::$app->request->getBodyParam("venue_operating_hours");
        $model->venue_restrictions = Yii::$app->request->getBodyParam("venue_restrictions");

        if (!$model->save()) {
            if (isset($model->errors)) {
                return [
                    "operation" => "error",
                    "message" => $model->errors
                ];
            } else {
                return [
                    "operation" => "error",
                    "message" => "We've faced a problem creating the venue"
                ];
            }
        }

        return [
            "operation" => "success",
            "message" => "Venue created successfully"
        ];
    }

}
