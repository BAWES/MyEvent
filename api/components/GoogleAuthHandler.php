<?php
namespace candidate\components;

use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

/**
 * AuthHandler handles successful authentification via Yii auth component
 */
class GoogleAuthHandler 
{
//    /**
//     * Specify the target environment so specify how to handle login (mobile/etc.)
//     * @var string
//     */
//    private $targetEnvironment;
//
//    /**
//     * @var ClientInterface
//     */
//    private $client;
//
//    public function __construct(ClientInterface $client, $targetEnv = "browser")
//    {
//        $this->client = $client;
//
//        $this->targetEnvironment = $targetEnv;
//    }
//
//    public function handle()
//    {
//    	$new = false;
//        if(!Yii::$app->user->isGuest) {
//            return null; 
//        }
//
//        $attributes = $this->client->getUserAttributes();
//
//        $email = ArrayHelper::getValue($attributes, 'email');
//        //$id = ArrayHelper::getValue($attributes, 'id');
//        //$nickname = ArrayHelper::getValue($attributes, 'displayName');
//        //$name = ArrayHelper::getValue($attributes, 'name');
//        $firstname = ArrayHelper::getValue($attributes, 'given_name');
//        $lastname = ArrayHelper::getValue($attributes, 'family_name');
//        $gender = ArrayHelper::getValue($attributes, 'gender');
//        
//        $model = Candidate::find()
//            ->where(['email' => $email])
//            ->one();
//        
//        if ($model) {
//            //There's already an agent with this email, update his details
//            //And create an auth record for him and log him in
//
//            $model->email_verification = Candidate::EMAIL_VERIFIED;
//            
//            if($attributes['picture'])
//                $model->setProfileByUrl($attributes['picture']); 
//            
//            $model->generateOtp();
//            
//            $model->save(false);
//            
//            Yii::$app->user->login($model);     
//            
//            $new = $model->dob ? 0 : 1;
//            
//        } else {
//            
//            $model = new Candidate; 
//            $model->scenario = "signup-google";
//            $model->firstname = $firstname;
//            $model->lastname = $lastname;
//            $model->email = $email;
//            $model->email_verification = Candidate::EMAIL_VERIFIED;
//            $model->gender = self::detectGender($gender);
//            $model->generateOtp();
//            $model->language_pref = Yii::$app->language;
//            $model->setPassword(Yii::$app->security->generateRandomString(6));
//            
//            if($attributes['picture'])
//                $model->setProfileByUrl($attributes['picture']); 
//            
//            //$model->setCurrencyByIp(); 
//            
//            if (!$model->signup(false)) {
//                $cookie = new Cookie([
//                    'name' => 'message',
//                    'value' => Yii::t('job', "Unable to login."),
//                    'expire' => time() + 86400,
//                    'domain' => Yii::$app->params['cookieDomain'],
//                    'httpOnly' => false,
//                    'secure' => true, 
//                ]);
//                
//                $cookie->sameSite = PHP_VERSION_ID >= 70300 ? 'None' : null;
//                        
//                \Yii::$app->getResponse()->getCookies()->add($cookie);
//            } else {	  
//                $new = true;
//                Yii::$app->user->login($model);         
//            }
//        }
//        
//        if(!Yii::$app->user->isGuest)
//        {
//            $cookie = new Cookie([
//                'name' => 'otp',
//                'value' => $model->otp,
//                'expire' => time() + 60 * 5,//in 5 minutes 
//                'domain' => Yii::$app->params['cookieDomain'],
//                'httpOnly' => false,
//                'secure' => true
//            ]);
//            
//            $cookie->sameSite = PHP_VERSION_ID >= 70300 ? 'None' : null;
//                        
//            \Yii::$app->getResponse()->getCookies()->add($cookie);
//        }
//
//        /**
//         * Redirect with values stored in cookie
//         */
//	    if ($new) {
//	    	$url = Yii::$app->params['frontAppUrl']."welcome";
//		    $script = "
//            <script>
//            window.location = '".$url."';
//            </script>
//            ";
//	    } else {
//		    $script = "
//            <script>
//            window.location = '".Yii::$app->params['frontAppUrl']."';
//            </script>
//            ";
//	    }
//        
//
//        Yii::$app->response->content = $script;
//        return Yii::$app->response;
//    }
//	
//    /**
//     * @param $gender
//     *
//     * @return int
//     */
//    private static function detectGender($gender){
//    	if ($gender == 'male') {
//		    return Candidate::GENDER_MALE;
//	    } else if ($gender == 'other') {
//		    return Candidate::GENDER_OTHER;
//	    } else {
//		    return Candidate::GENDER_FEMALE;
//	    }
//    }
}
