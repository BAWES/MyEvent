<?php
namespace api\tests;


use Codeception\Util\HttpCode;

class AuthCest
{
    public $token;

//    public function _fixtures()
//    {
//        return [
//            'candidateToken' => CandidateTokenFixture::className(),
//            'country' => \common\fixtures\CountryFixture::className(),
//            'language' => CandidateLanguageFixture::className(),
//            'degree' => DegreeFixture::className(),
//            'school' => SchoolFixture::className(),
//            'cityFixtures' => CityFixture::className()
//        ];
//    }
//    
//    public function _before(FunctionalTester $I)
//    {
//        $this->token = CandidateToken::find()
//            ->one()
//            ->token_value;
//    }
//
//    /**
//     * Login
//     * @param FunctionalTester $I
//     */
//    public function tryToLogin(FunctionalTester $I)
//    {
//        $candidate = Candidate::find()->one();
//        
//        $I->wantTo('Validate auth > login api');
//        $I->amHttpAuthenticated($candidate->email, '123456');
//        $I->sendGET('v1/auth/login');
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->canSeeResponseContainsJson(["email"=>$candidate->email]);
//    }
//    
//    /**
//     * Request Password Reset Token
//     * @param FunctionalTester $I
//     */
//    public function tryToRequestResetPassword(FunctionalTester $I)
//    {
//        $candidate = Candidate::find()->one();
//
//        $I->wantTo('Try to request password reset token');
//        $I->sendPOST('v1/auth/request-reset-password', [
//            'email' => $candidate->email
//        ]);
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->canSeeResponseContainsJson();
//    }
//    
//    /**
//     * Update Password
//     * @param FunctionalTester $I
//     */
//    public function tryToUpdatePassword(FunctionalTester $I)
//    {
//        $candidate = Candidate::find()->one();
//
//        //set password token manually because API will work on different DB
//        $candidate->generatePasswordResetToken();
//        $candidate->save(false);
//
//        $I->wantTo('Try to update password by reset token');
//        $I->sendPATCH('v1/auth/update-password', [
//            'token' => $candidate->password_reset_token,
//            'newPassword' => '123456'
//        ]);
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->canSeeResponseContainsJson();
//    }
//    
//    /**
//     * Signup
//     * @param FunctionalTester $I
//     */
//    public function tryToSignup(FunctionalTester $I)
//    {   
//        $I->wantTo('Try to signup');
//        $I->sendPOST('v1/auth/signup', [
//            'username' => 'Dhvani92',
//            'firstname' => 'Dhvani',
//            'lastname' => 'Pandya',
//            'email' => 'dhvani@localhost.com',
//            'password_hash' => '123456',
//            'dob' => date('Y-m-d', strtotime('-17 year')),
//            'objective' => 'To become par of great things',
//            'contact_number' => 9898989898,
//            'gender' => 1,
//            'auth_key' => \Yii::$app->security->generateRandomString(6),
//        ]);
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->seeResponseContainsJson();
//    }
//    
//    /**
//     * Resend Verification Email
//     * @param FunctionalTester $I
//     */
//    public function tryToResendVerificationEmail(FunctionalTester $I)
//    {   
//        $model = Candidate::find()->one();
//        $model->email_verification = 0;
//        $model->save(false);
//        
//        $I->wantTo('Try to get verification again by email');
//        $I->sendPOST('v1/auth/resend-verification-email', [
//            'email' => $model->email
//        ]);
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->seeResponseContainsJson();
//    }
//    
//    /**
//     * Resend Verify Email
//     * @param FunctionalTester $I
//     */
//    public function tryToVerifyEmail(FunctionalTester $I)
//    {
//        $candidate = new \candidate\models\Candidate();
//        $candidate->firstname = 'cron testing unique name @ ' . time();
//        $candidate->lastname = 'test';
//        $candidate->email = 'demo@localhost.com';
//        $candidate->password_hash = 'test';
//        $candidate->currency_pref = 'USD';
//        $candidate->is_password_set = true;
//        $candidate->candidate_job_search_status = Candidate::JOBSEEK_ACTIVELY_LOOKING_FOR_JOB;
//        $candidate->dob = '1992-12-15';
//        $candidate->contact_number = '89457839758';
//        $candidate->gender = Candidate::GENDER_MALE;
//        $candidate->objective = 'Objective is no more objective';
//        $candidate->auth_key = '1221212';
//        $candidate->status = Candidate::STATUS_ACTIVE;
//        $candidate->nationality_country_uuid = \common\models\Country::find()->one()->country_uuid;
//        $candidate->city_uuid = \common\models\City::find()->one()->city_uuid;
//        $candidate->email_verification = Candidate::EMAIL_NOT_VERIFIED;
//        $candidate->save(false);
//
//        $I->wantTo('Try to verify email by code');
//        $I->sendPOST('v1/auth/verify-email', [
//            'code' => $candidate->auth_key,
//            'email' => $candidate->email
//        ]);
//        $I->seeResponseCodeIs(HttpCode::OK); // 200
//        $I->seeResponseContainsJson();
//    }
}
