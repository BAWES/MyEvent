<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */

$this->title = 'Update Venue: ' . $model->venue_uuid;
$this->params['breadcrumbs'][] = ['label' => 'Venues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->venue_uuid, 'url' => ['view', 'id' => $model->venue_uuid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="venue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
