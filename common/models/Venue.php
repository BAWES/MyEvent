<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;

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
 * @property int $venue_approved [Whether this venue is the one on display at frontend]
 * @property string $venue_contact_email
 * @property string|null $venue_contact_phone
 * @property string|null $venue_contact_website
 * @property int|null $venue_capacity_minimum
 * @property int|null $venue_capacity_maximum
 * @property string|null $venue_operating_hours
 * @property string|null $venue_restrictions
 *
 * @property User $user
 * @property VenueOccasion[] $venueOccasions
 * @property VenuePhoto[] $venuePhotos
 */
class Venue extends \yii\db\ActiveRecord {

    public $venue_occasions;
    public $venue_photos;

    //venue_approved  values
    const DRAFT = 0;
    const ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'venue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['venue_uuid'], 'string', 'max' => 36],
            [['venue_uuid'], 'unique'],
            [['user_uuid'], 'string', 'max' => 36],
            [['user_uuid', 'venue_name', 'venue_contact_email'], 'required'],
            [['venue_capacity_minimum', 'venue_capacity_maximum'], 'integer'],
            [['venue_location_longitude', 'venue_location_latitude'], 'number'],
            [['venue_description', 'venue_operating_hours', 'venue_restrictions'], 'string'],
            [['venue_name', 'venue_location', 'venue_contact_email', 'venue_contact_phone', 'venue_contact_website'], 'string', 'max' => 255],
            //URL Validator
            [['venue_contact_website'], 'url', 'defaultScheme' => 'http'],
            [['venue_photos'], 'file', 'extensions' => 'jpg, jpeg , png', 'maxFiles' => 5],
            ['venue_approved', 'in', 'range' => [self::DRAFT, self::ACTIVE]],
            [['venue_contact_email'], 'unique'],
            [['user_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_uuid' => 'user_uuid']],
//            [['occasion_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Occasion::className(), 'targetAttribute' => ['occasion_uuid' => 'occasion_uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'venue_uuid' => 'Venue Uuid',
            'user_uuid' => 'User Uuid',
//            'occasion_uuid' => 'Occasion Uuid',
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
     * 
     * @return type
     */
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'venue_uuid',
                ],
                'value' => function() {
                    if (!$this->venue_uuid)
                        $this->venue_uuid = Yii::$app->db->createCommand('SELECT uuid()')->queryScalar();

                    return $this->venue_uuid;
                }
            ]
        ];
    }

    /**
     * Upload venue photos to cloudinary
     * @param type $imageURL
     */
    public function uploadVenuePhoto($imageURL) {

        $filename = Yii::$app->security->generateRandomString();
        try {
            $result = Yii::$app->cloudinaryManager->upload(
                    $imageURL, [
                'public_id' => "venue-photos/" . $filename
                    ]
            );

            Yii::error('Cloudinary Result: ' . json_encode($result));

            if ($result || count($result) > 0) {
                Yii::error('before saving venue photo');

                $MediaConnections = new VenuePhoto();
                $MediaConnections->venue_uuid = $this->venue_uuid;
                $MediaConnections->photo_url = basename($result['url']);
                if ($MediaConnections->save())
                    Yii::error('after saving venue photo0->' . json_encode($MediaConnections));
                else
                    Yii::error('errroorrr' . json_encode($MediaConnections->errors));
            }
        } catch (\Exception $ex) {
            Yii::error('Error when uploading venue photos to Cloudinry: ' . json_encode($ex));
        }
    }

    /**
     * Delete all venue photos
     */
    public function deleteAllVenuePhotos() {
        $venuePhotos = VenuePhoto::find()->where(['venue_uuid' => $this->venue_uuid])->all();
        if ($venuePhotos) {
            foreach ($venuePhotos as $venuePhoto) 
                $venuePhoto->delete();
        }
    }
    
    public function beforeDelete() {
        $this->deleteAllVenuePhotos();
        return parent::beforeDelete();
    }

    /**
     * Promotes current venue to draft venue while disabling rest
     */
    public function promoteToDraftVenue() {
        $this->venue_approved = Venue::DRAFT;
        $this->save(false);
    }

    /**
     * Promotes current venue to active venue while disabling rest
     */
    public function promoteToActiveVenue() {
        $this->venue_approved = Venue::ACTIVE;
        $this->save(false);
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if (is_array($this->venue_occasions)) {
            Yii::error('is array');
            foreach ($this->venue_occasions as $occasion_uuid) {
                $venue_occasion_model = new VenueOccasion;
                $venue_occasion_model->occasion_uuid = $occasion_uuid;
                $venue_occasion_model->venue_uuid = $this->venue_uuid;
                $venue_occasion_model->save();
            }
        } else {
            Yii::error('not array->' . gettype($this->venue_occasions));
            Yii::error('$this->venue_occasions' . json_encode($this->venue_occasions));
            Yii::error('$this->venue_occasions' . ($this->venue_occasions));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['user_uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueOccasions() {
        return $this->hasMany(VenueOccasion::className(), ['venue_uuid' => 'venue_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenuePhotos() {
        return $this->hasMany(VenuePhoto::className(), ['venue_uuid' => 'venue_uuid']);
    }

}
