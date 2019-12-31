<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Occasions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occasion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Occasion', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'occasion_uuid',
            'occasion_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
