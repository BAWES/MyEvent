<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue_occasion".
 *
 * @property int $venue_uuid
 * @property int $occasion_uuid
 *
 * @property Occasion $occasionUu
 * @property Venue $venueUu
 */
class VenueOccasion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venue_occasion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['venue_uuid', 'occasion_uuid'], 'required'],
            [['venue_uuid', 'occasion_uuid'], 'integer'],
            [['occasion_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Occasion::className(), 'targetAttribute' => ['occasion_uuid' => 'occasion_uuid']],
            [['venue_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Venue::className(), 'targetAttribute' => ['venue_uuid' => 'venue_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'venue_uuid' => 'Venue Uuid',
            'occasion_uuid' => 'Occasion Uuid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOccasionUu()
    {
        return $this->hasOne(Occasion::className(), ['occasion_uuid' => 'occasion_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueUu()
    {
        return $this->hasOne(Venue::className(), ['venue_uuid' => 'venue_uuid']);
    }
}
