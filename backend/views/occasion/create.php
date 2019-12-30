<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Occasion */

$this->title = 'Create Occasion';
$this->params['breadcrumbs'][] = ['label' => 'Occasions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occasion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
