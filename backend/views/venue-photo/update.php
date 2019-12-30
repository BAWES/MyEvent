<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VenuePhoto */

$this->title = 'Update Venue Photo: ' . $model->photo_uuid;
$this->params['breadcrumbs'][] = ['label' => 'Venue Photos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->photo_uuid, 'url' => ['view', 'id' => $model->photo_uuid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="venue-photo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
