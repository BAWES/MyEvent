<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue".
 *
 * @property int $venue_uuid
 * @property int $user_uuid
 * @property string $venue_name
 * @property string|null $venue_location
 * @property float|null $venue_location_longitude
 * @property float|null $venue_location_latitude
 * @property string|null $venue_description
 * @property int $venue_approved
 * @property string $venue_contact_email
 * @property string|null $venue_contact_phone
 * @property string|null $venue_contact_website
 * @property int|null $venue_capacity_minimum
 * @property int|null $venue_capacity_maximum
 * @property string|null $venue_operating_hours
 * @property string|null $venue_restrictions
 *
 * @property User $userUu
 * @property VenueOccasion[] $venueOccasions
 * @property VenuePhoto[] $venuePhotos
 */
class Venue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_uuid', 'venue_name', 'venue_contact_email'], 'required'],
            [['user_uuid', 'venue_approved', 'venue_capacity_minimum', 'venue_capacity_maximum'], 'integer'],
            [['venue_location_longitude', 'venue_location_latitude'], 'number'],
            [['venue_description', 'venue_operating_hours', 'venue_restrictions'], 'string'],
            [['venue_name', 'venue_location', 'venue_contact_email', 'venue_contact_phone', 'venue_contact_website'], 'string', 'max' => 255],
            [['venue_contact_email'], 'unique'],
            [['venue_contact_phone'], 'unique'],
            [['user_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_uuid' => 'user_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'venue_uuid' => 'Venue Uuid',
            'user_uuid' => 'User Uuid',
            'venue_name' => 'Venue Name',
            'venue_location' => 'Venue Location',
            'venue_location_longitude' => 'Venue Location Longitude',
            'venue_location_latitude' => 'Venue Location Latitude',
            'venue_description' => 'Venue Description',
            'venue_approved' => 'Venue Approved',
            'venue_contact_email' => 'Venue Contact Email',
            'venue_contact_phone' => 'Venue Contact Phone',
            'venue_contact_website' => 'Venue Contact Website',
            'venue_capacity_minimum' => 'Venue Capacity Minimum',
            'venue_capacity_maximum' => 'Venue Capacity Maximum',
            'venue_operating_hours' => 'Venue Operating Hours',
            'venue_restrictions' => 'Venue Restrictions',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUu()
    {
        return $this->hasOne(User::className(), ['user_uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueOccasions()
    {
        return $this->hasMany(VenueOccasion::className(), ['venue_uuid' => 'venue_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenuePhotos()
    {
        return $this->hasMany(VenuePhoto::className(), ['venue_uuid' => 'venue_uuid']);
    }
}
