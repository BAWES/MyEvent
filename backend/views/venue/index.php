<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Venues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Venue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'venue_uuid',
            'user_uuid',
            'venue_name',
            'venue_location',
            'venue_location_longitude',
            //'venue_location_latitude',
            //'venue_description:ntext',
            //'venue_approved',
            //'venue_contact_email:email',
            //'venue_contact_phone',
            //'venue_contact_website',
            //'venue_capacity_minimum',
            //'venue_capacity_maximum',
            //'venue_operating_hours:ntext',
            //'venue_restrictions:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
