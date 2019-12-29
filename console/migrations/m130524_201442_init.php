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
        $this->createTable('{{%user}}', [
            'user_uuid' => $this->primaryKey(),
            'user_name' => $this->string()->notNull()->unique(),
            'user_email' => $this->string()->notNull()->unique(),
            'user_auth_key' => $this->string(32)->notNull(),
            'user_password_hash' => $this->string()->notNull(),
            'user_password_reset_token' => $this->string()->unique(),
            'user_status' => $this->smallInteger()->notNull()->defaultValue(10),
            'user_created_at' => $this->integer()->notNull(),
            'user_updated_at' => $this->integer()->notNull(),
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
        
    
        
        
        $this->dropTable('{{%user}}');
        
        $this->dropTable('{{%occasion}}');
        
        $this->dropTable('{{%venue}}');
        
        $this->dropTable('{{%venue_occasion}}');
    }
}
