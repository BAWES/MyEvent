<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'admin_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_auth_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_password_reset_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_status')->textInput() ?>

    <?= $form->field($model, 'admin_created_at')->textInput() ?>

    <?= $form->field($model, 'admin_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
