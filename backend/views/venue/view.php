<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Venue;
use slavkovrn\imagecarousel\ImageCarouselWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = $model->venue_name;
$this->params['breadcrumbs'][] = ['label' => 'Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="venue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p style='text-align: center'>
        <?= Html::a('Update', ['update', 'id' => $model->venue_uuid], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->venue_uuid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>

        <?php if ($model->venue_approved != Venue::DRAFT) { ?>
            <?=
            Html::a('Revert to Draft', ['promote-to-draft', 'id' => $model->venue_uuid], [
                'class' => 'btn btn-warning',
                'data' => [
//                    'confirm' => 'Are you sure you want to promote this project to draft?',
                    'method' => 'post',
                ],
            ])
            ?>
        <?php } ?>

        <?php if ($model->venue_approved != Venue::ACTIVE) { ?>
            <?=
            Html::a('Promote to Active Venue', ['promote-to-active', 'id' => $model->venue_uuid], [
                'class' => 'btn btn-success',
                'data' => [
//                    'confirm' => 'Are you sure you want to promote this project to active?',
                    'method' => 'post',
                ],
            ])
            ?>
        <?php } ?>



    </p>

    <?=
    DetailView::widget([
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
    ])
    ?>

    <?php
    foreach ($model->getVenuePhotosURL() as $venuePhotoUrl) {
        $venuePhotos [] = [
            'src' => $venuePhotoUrl,
        ];
    }


    echo ImageCarouselWidget::widget([
        'id' => 'image-carousel', // unique id of widget
        'width' => 960, // width of widget container
        'height' => 300, // height of widget container
        'img_width' => 320, // width of central image
        'img_height' => 180, // height of central image
        'images' => $venuePhotos
    ])
    ?>



</div>
