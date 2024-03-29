<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->user_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p style='text-align: center'>
        <?= Html::a('Update', ['update', 'id' => $model->user_uuid], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->user_uuid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>

        <?php if ($model->user_email_verified == User::EMAIL_NOT_VERIFIED) { ?>
            <?=
            Html::a('Verify', ['verify-user', 'id' => $model->venue_uuid], [
                'class' => 'btn btn-success',
                'data' => [
//                    'confirm' => 'Are you sure you want to promote this project to draft?',
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
            // 'user_uuid',
            'user_name',
            'user_email:email',
            [
                'label' => 'Password',
                'value' => '***',
            ],
            [
                'label' => 'Status',
                'value' => $model->user_status,
            ],
            'user_created_at:datetime',
            'user_updated_at:datetime',
        ],
    ])
    ?>

</div>
