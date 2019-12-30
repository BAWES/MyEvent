<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "occasion".
 *
 * @property int $occasion_uuid
 * @property string $occasion_name
 *
 * @property VenueOccasion[] $venueOccasions
 */
class Occasion extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'occasion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['occasion_name'], 'required'],
            [['occasion_uuid'], 'string', 'max' => 36],
            [['occasion_uuid'], 'unique'],
            [['occasion_name'], 'string', 'max' => 255],
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
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'occasion_uuid',
                ],
                'value' => function() {
                    if (!$this->occasion_uuid)
                        $this->occasion_uuid = Yii::$app->db->createCommand('SELECT uuid()')->queryScalar();

                    return $this->occasion_uuid;
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'occasion_uuid' => 'Occasion Uuid',
            'occasion_name' => 'Occasion Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVenueOccasions() {
        return $this->hasMany(VenueOccasion::className(), ['occasion_uuid' => 'occasion_uuid']);
    }

}
