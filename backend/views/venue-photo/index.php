<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Venue Photos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-photo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Venue Photo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'photo_uuid',
            'venue_uuid',
            'photo_url:url',
            'photo_created_datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
