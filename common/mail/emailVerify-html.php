<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->user_name) ?>,</p>

    <p>Follow the link below to verify your email:</p>

    <p><?= $verificationUrl ?></p>
</div>
