<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="venue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_uuid')->textInput() ?>

    <?= $form->field($model, 'venue_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'venue_approved')->textInput() ?>

    <?= $form->field($model, 'venue_contact_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_contact_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_contact_website')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_capacity_minimum')->textInput() ?>

    <?= $form->field($model, 'venue_capacity_maximum')->textInput() ?>

    <?= $form->field($model, 'venue_operating_hours')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'venue_restrictions')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
