<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue_photo".
 *
 * @property int $photo_uuid
 * @property int $venue_uuid
 * @property string|null $photo_url
 * @property string $photo_created_datetime
 *
 * @property Venue $venueUu
 */
class VenuePhoto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venue_photo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['venue_uuid', 'photo_created_datetime'], 'required'],
            [['venue_uuid'], 'integer'],
            [['photo_created_datetime'], 'safe'],
            [['photo_url'], 'string', 'max' => 255],
            [['venue_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Venue::className(), 'targetAttribute' => ['venue_uuid' => 'venue_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'photo_uuid' => 'Photo Uuid',
            'venue_uuid' => 'Venue Uuid',
            'photo_url' => 'Photo Url',
            'photo_created_datetime' => 'Photo Created Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueUu()
    {
        return $this->hasOne(Venue::className(), ['venue_uuid' => 'venue_uuid']);
    }
}
