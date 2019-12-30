<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = $model->venue_uuid;
$this->params['breadcrumbs'][] = ['label' => 'Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="venue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->venue_uuid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->venue_uuid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'venue_uuid',
            'user_uuid',
            'venue_name',
            'venue_location',
            'venue_location_longitude',
            'venue_location_latitude',
            'venue_description:ntext',
            'venue_approved',
            'venue_contact_email:email',
            'venue_contact_phone',
            'venue_contact_website',
            'venue_capacity_minimum',
            'venue_capacity_maximum',
            'venue_operating_hours:ntext',
            'venue_restrictions:ntext',
        ],
    ]) ?>

</div>
