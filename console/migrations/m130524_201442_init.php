<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /**
         * Create Admin Table
         */
        $this->createTable('{{%admin}}', [
            'admin_id' => $this->primaryKey(),
            'admin_name' => $this->string()->notNull()->unique(),
            'admin_email' => $this->string()->notNull()->unique(),
            'admin_auth_key' => $this->string(32)->notNull(),
            'admin_password_hash' => $this->string()->notNull(),
            'admin_password_reset_token' => $this->string()->unique(),
            'admin_status' => $this->smallInteger()->notNull()->defaultValue(10),
            'admin_created_at' => $this->datetime()->notNull(),
            'admin_updated_at' => $this->datetime()->notNull(),
        ], $tableOptions);
        
        // Add Saoud as Base Admin
        $sql = 'INSERT INTO admin SET
            admin_id = 1,
            admin_name = "Saoud AlTurki",
            admin_email = "saoud@bawes.net",
            admin_auth_key = "Lu4vPW4Npfgce6WkXdt9OErpxXdB7GW4",
            admin_password_hash = "$2y$13$LdNaUZOdyyL5.TYl/tbfI.i9YVkhxFd/9LTzaaCFgn4lCeTNgL8le",
            admin_password_reset_token = NULL,
            admin_status = 10,
            admin_created_at = "2018-08-21 19:20:58",
            admin_updated_at = "2018-08-21 19:40:58"
            ';
        Yii::$app->db->createCommand($sql)->execute();
        
        // Add Khalid as Base Admin
        $sql = 'INSERT INTO admin SET
            admin_id = 2,
            admin_name = "Khalid AlMutawa",
            admin_email = "khalid@bawes.net",
            admin_auth_key = "Lu4vPW4Npfgce6WkXdt9OErpxXdB7GW4",
            admin_password_hash = "$2y$13$uJ2Gcw66dRJ7G/S0V3e6turULkLnJjldzgnapvH0t.csMpWcOePgi",
            admin_password_reset_token = NULL,
            admin_status = 10,
            admin_created_at = "2018-08-21 19:20:58",
            admin_updated_at = "2018-08-21 19:40:58"
            ';
        Yii::$app->db->createCommand($sql)->execute();
        
        /**
         * Create User Table
         */
        $this->createTable('{{%user}}', [
            'user_uuid' => $this->primaryKey(),
            'user_name' => $this->string()->notNull()->unique(),
            'user_email' => $this->string()->notNull()->unique(),
            'user_auth_key' => $this->string(32)->notNull(),
            'user_password_hash' => $this->string()->notNull(),
            'user_password_reset_token' => $this->string()->unique(),
            'user_status' => $this->smallInteger()->notNull()->defaultValue(10),
            'user_created_at' => $this->datetime()->notNull(),
            'user_updated_at' => $this->datetime()->notNull(),
        ], $tableOptions);
        
         /**
         * Create Occasion Table
         * Occasion for an event. Such as wedding, birthday, valentines, etc.
         */
         $this->createTable('{{%occasion}}', [
            'occasion_uuid' => $this->primaryKey(),
            'occasion_name' => $this->string()->notNull(),
        ], $tableOptions);
         
         /**
         * Create Venue Table
         */
         $this->createTable('{{%venue}}', [
            'venue_uuid' => $this->primaryKey(),
            'user_uuid' => $this->integer()->notNull(),
            'venue_name' => $this->string()->notNull(),
            'venue_location' => $this->string(),
            'venue_location_longitude' => $this->decimal(9, 6),
            'venue_location_latitude' => $this->decimal(9, 6),
            'venue_description' => $this->text(),
            'venue_approved' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'venue_contact_email' => $this->string()->notNull()->unique(),
            'venue_contact_phone' => $this->string()->unique(),
            'venue_contact_website' => $this->string(255),
            'venue_capacity_minimum' => $this->integer(),
            'venue_capacity_maximum' => $this->integer(),
            'venue_operating_hours' => $this->text(),
            'venue_restrictions' => $this->text(),
        ], $tableOptions);
         
         // creates index for column `user_uuid` in venue table
         $this->createIndex(
             'idx-venue-user_uuid',
             'venue',
             'user_uuid'
         );


         // add foreign key for `user_uuid` in table `venue`
         $this->addForeignKey(
             'fk-venue-user_uuid',
             'venue',
             'user_uuid',
             'user',
             'user_uuid',
             'CASCADE'
         );
         
        
         /**
         * Create Venue Occasion Table
         * Relation between venue and occasion
         */
         $this->createTable('{{%venue_occasion}}', [
            'venue_uuid' => $this->integer()->notNull(),
            'occasion_uuid' => $this->integer()->notNull(),
            
        ], $tableOptions);
         
         // creates index for column `venue_uuid` in venue_occasion table
         $this->createIndex(
             'idx-venue-occasion-venue_uuid',
             'venue_occasion',
             'venue_uuid'
         );

         // add foreign key for `venue_uuid` in table `venue`
         $this->addForeignKey(
             'fk-venue-occasion-venue_uuid',
             'venue_occasion',
             'venue_uuid',
             'venue',
             'venue_uuid',
             'CASCADE'
         );
         
                 
         
         // creates index for column `occasion_uuid` in venue_occasion table
         $this->createIndex(
             'idx-venue-occasion-occasion_uuid',
             'venue_occasion',
             'occasion_uuid'
         );

         // add foreign key for `occasion_uuid` in table `venue`
         $this->addForeignKey(
             'fk-venue-occasion-occasion_uuid',
             'venue_occasion',
             'occasion_uuid',
             'occasion',
             'occasion_uuid',
             'CASCADE'
         );
         
        /**
         * Create Venue Photo Table
         * Photos of a venue
         */
         $this->createTable('{{%venue_photo}}', [
            'photo_uuid' => $this->primaryKey(),
            'venue_uuid' => $this->integer()->notNull(),
            'photo_url' => $this->string(),
            'venue_uuid' => $this->integer()->notNull(),
            'photo_created_datetime' => $this->datetime()->notNull(),

        ], $tableOptions);
         
                 
        // creates index for column `venue_uuid` in venue_photo table
         $this->createIndex(
             'idx-venue-photo-venue_uuid',
             'venue_photo',
             'venue_uuid'
         );

         // add foreign key for `venue_uuid` in table `venue_photo`
         $this->addForeignKey(
             'fk-venue-photo-venue_uuid',
             'venue_photo',
             'venue_uuid',
             'venue',
             'venue_uuid',
             'CASCADE'
         );
         
    }

    public function down()
    {
        // drops ForeignKey for column `user_uuid`
        $this->dropForeignKey(
            'fk-venue-user_uuid',
            'venue'
        ); 
        
        // drops index for column `user_uuid`
        $this->dropIndex(
            'idx-venue-user_uuid',
            'venue'
        );
        
   
        // drops ForeignKey for column `venue_uuid`
        $this->dropForeignKey(
            'fk-venue-occasion-venue_uuid',
            'venue_occasion'
        );   
        
        // drops index for column `venue_uuid`
        $this->dropIndex(
            'idx-venue-occasion-venue_uuid',
            'venue_occasion'
        );
        
        // drops ForeignKey for column `occasion_uuid`
        $this->dropForeignKey(
            'fk-venue-occasion-occasion_uuid',
            'venue_occasion'
        );   
        
       // drops index for column `occasion_uuid`
        $this->dropIndex(
            'idx-venue-occasion-occasion_uuid',
            'venue_occasion'
        );
        
    
        // drops ForeignKey for column `venue_uuid`
        $this->dropForeignKey(
            'fk-venue-photo-venue_uuid',
            'venue_photo'
        ); 
        
        // drops index for column `venue_uuid`
        $this->dropIndex(
            'idx-venue-photo-venue_uuid',
            'venue_photo'
        );
        
        //Drop all tables
        $this->dropTable('{{%admin}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%occasion}}');
        $this->dropTable('{{%venue}}');
        $this->dropTable('{{%venue_occasion}}');
        $this->dropTable('{{%venue_photo}}');
    }
}
