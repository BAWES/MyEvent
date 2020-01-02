<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use api\components\GoogleAuthHandler;


/**
 * Site controller
 */
class SiteController extends Controller
{    
    /**
     * Set redirect url for google client and redirect to Google for auth
     */
    public function actionAuth() {
        
        $language = Yii::$app->request->get('language');
        
        if ($language && $language != Yii::$app->language) 
        {
            Yii::$app->language = $language;
        }
        
        $collection = Yii::$app->get('authClientCollection');
        $client = $collection->getClient('google');
        $client->setReturnUrl(Url::to(['site/auth', 'authclient' => 'google', 'language' => Yii::$app->language], true));
        
        $handler = new \yii\authclient\AuthAction('auth', 'site'); 
        $handler->successCallback = function($client) {
            return (new GoogleAuthHandler($client))->handle();        
        };
        $handler->run();
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}