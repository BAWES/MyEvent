<?php

namespace common\components;

use yii\httpclient\Client;
use common\models\City;


class GoogleMap {
    
    public $accessKey; 
    
    private $endPoint = 'https://maps.googleapis.com/maps/api/';
    
    private $client; 
    
    public function __construct() {
        $this->client = new Client(['baseUrl' => $this->endPoint]);
    }

    /**
     * return list of places by keyword 
     * @return type
     */
    public function getPlacePredictions($query) {

        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl('place/autocomplete/json')
            ->setData([
                'types' => '(regions)',//(cities)
                'input' => $query,
                'key' => $this->accessKey])
            ->send();
        
        return $response->getData()['predictions'];
    }
    
    /**
     * Return place detail by google map place_id
     * @param string $place_id
     * @param string $name
     * @param string $country_name
     * @return type
     */
    public function placeDetail($place_id, $name = null, $country_name = null) {
        
        if($name && $country_name) {
            $model = $this->_isExists($name, $country_name);
            
            if($model)
            {

                return [
                    'operation' => 'success',
                    'city' => $model
                ];
            }
        }

        $url = $this->endPoint . 'place/details/json?placeid=' . $place_id;
        
        return City::addByGoogleAPIResponse($url, null, $name);
    }

    /**
     * Check if already in DB 
     */
    private function _isExists($city_name, $country_name) {

        //if it's area 
        
        //TODO: return city with county by area 
        
        
        $cityQuery = City::find()   
            ->joinWith(['country'])
            ->andWhere([
                    'OR',
                    [
                        'city_name_en' => $city_name
                    ],
                    [
                        'city_name_ar' => $city_name
                    ]
                ]);
        
        //can have same city name in different country 
        
        if($country_name)  
        {
            $cityQuery->andWhere([
                'OR',
                [
                    'country_name_en' => $country_name
                ],
                [
                    'country_name_ar' => $country_name
                ],
            ]);
        }

        $model = $cityQuery->with('country')
            ->asArray()
            ->one();
        
        return  $model;
    }
}