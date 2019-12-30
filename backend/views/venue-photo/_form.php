<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VenuePhoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="venue-photo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'venue_uuid')->textInput() ?>

    <?= $form->field($model, 'photo_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'photo_created_datetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
