<?php

namespace api\models;


/**
 * This is the model class for table "user".
 * It extends from \common\models\User but with custom functionality for User module
 * 
 */
class User extends \common\models\User {

    public function rules() {
        return parent::rules();
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['user_auth_key'], 
            $fields['user_password_hash'], 
            $fields['user_password_reset_token'], 
            $fields['user_status'], 
            $fields['user_email_verified'], 
            $fields['user_limit_email'], 
            $fields['user_created_at'], 
            $fields['user_updated_at']);
        
        return $fields;
    }
}
