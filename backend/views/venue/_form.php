<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Occasion;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Venue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="venue-form">

    <?php
    $userQuery = User::find()->asArray()->all();
    $userArray = ArrayHelper::map($userQuery, 'user_uuid', 'user_name');

    $occasionQuery = Occasion::find()->asArray()->all();
    $occasionArray = ArrayHelper::map($occasionQuery, 'occasion_uuid', 'occasion_name');

    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    
    ?>

    <?= $form->field($model, 'user_uuid')->dropDownList($userArray, ['prompt' => 'Select...'])->label('Username') ?>

    <?= $form->field($model, 'occasion_uuid')->dropDownList($occasionArray, ['prompt' => 'Select...'])->label('Occasion') ?>

    <?= $form->field($model, 'venue_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_location_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'venue_contact_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_contact_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_contact_website')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'venue_capacity_minimum')->textInput() ?>

    <?= $form->field($model, 'venue_capacity_maximum')->textInput() ?>

    <?= $form->field($model, 'venue_operating_hours')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'venue_restrictions')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'venue_photos[]')->fileInput(['multiple' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
