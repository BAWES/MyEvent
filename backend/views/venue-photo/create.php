<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VenuePhoto */

$this->title = 'Create Venue Photo';
$this->params['breadcrumbs'][] = ['label' => 'Venue Photos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="venue-photo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
