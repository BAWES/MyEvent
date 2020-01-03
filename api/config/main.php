<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            // Accept and parse JSON Requests
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [ // AuthController
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/auth',
                    'pluralize' => false,
                    'patterns' => [
                        'GET login' => 'login',
                        'PATCH verify-email' => 'verify-email',
                        'PATCH update-password' => 'update-password',
                        'POST create-account' => 'create-account',
                        'POST request-reset-password' => 'request-reset-password',
//                        'POST resend-verification-email' => 'resend-verification-email',
//                        'POST validate' => 'validate',
                        // OPTIONS VERBS
                        'OPTIONS verify-email' => 'options',
//                        'OPTIONS validate' => 'options',
                        'OPTIONS login' => 'options',
                        'OPTIONS create-account' => 'options',
                        'OPTIONS update-password' => 'options',
                        'OPTIONS request-reset-password' => 'options',
//                        'OPTIONS resend-verification-email' => 'options',
                    ]
                ],
  
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
