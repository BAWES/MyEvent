<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "venue_photo".
 *
 * @property int $photo_uuid
 * @property int $venue_uuid
 * @property string|null $photo_url
 * @property string $photo_created_datetime
 *
 * @property Venue $venue
 */
class VenuePhoto extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'venue_photo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['photo_uuid'], 'string', 'max' => 36],
            [['photo_uuid'], 'unique'],
            [['venue_uuid'], 'required'],
            [['venue_uuid'], 'string', 'max' => 36],
            [['photo_url'], 'string', 'max' => 255],
            [['venue_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Venue::className(), 'targetAttribute' => ['venue_uuid' => 'venue_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'photo_uuid' => 'Photo Uuid',
            'venue_uuid' => 'Venue Uuid',
            'photo_url' => 'Photo Url',
            'photo_created_datetime' => 'Photo Created Datetime',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'photo_uuid',
                ],
                'value' => function() {
                    if (!$this->photo_uuid)
                        $this->photo_uuid = Yii::$app->db->createCommand('SELECT uuid()')->queryScalar();

                    return $this->photo_uuid;
                }
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'photo_created_datetime',
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * 
     */
    public function afterDelete() {
        Yii::$app->cloudinaryManager->delete("venue-photos/" . $this->photo_url);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenue() {
        return $this->hasOne(Venue::className(), ['venue_uuid' => 'venue_uuid']);
    }

}
