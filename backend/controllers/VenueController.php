<?php

namespace backend\controllers;

use Yii;
use common\models\Venue;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\VenuePhoto;

/**
 * VenueController implements the CRUD actions for Venue model.
 */
class VenueController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [//allow authenticated users only
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Venue models.
     * @return mixed
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Venue::find(),
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Venue model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Venue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Venue();


        if ($model->load(Yii::$app->request->post())) {

            $model->venue_occasions = $_POST['Venue']['venue_occasions'];

            if ($model->save()) {
                $venue_photos = \yii\web\UploadedFile::getInstances($model, 'venue_photos');

                if (sizeof($venue_photos) > 0) {
                    foreach ($venue_photos as $venue_photo)
                        $model->uploadVenuePhoto($venue_photo->tempName);
                }


                return $this->redirect(['view', 'id' => $model->venue_uuid]);
            }
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Venue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $venue_photos = \yii\web\UploadedFile::getInstances($model, 'venue_photos');

            if (sizeof($venue_photos) > 0) {
             
                $model->deleteAllVenuePhotos();
                foreach ($venue_photos as $venue_photo)
                    $model->uploadVenuePhoto($venue_photo->tempName);
            }


            return $this->redirect(['view', 'id' => $model->venue_uuid]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Venue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Promotes a venue to become the active venue displayed on frontend
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPromoteToDraft($id) {
        $model = $this->findModel($id);
        $model->promoteToDraftVenue();

        return $this->redirect(['view', 'id' => $model->venue_uuid]);
    }

    /**
     * Promotes a venue to become the active venue displayed on frontend
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPromoteToActive($id) {
        $model = $this->findModel($id);
        $model->promoteToActiveVenue();

        return $this->redirect(['view', 'id' => $model->venue_uuid]);
    }

    /**
     * Finds the Venue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Venue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Venue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
