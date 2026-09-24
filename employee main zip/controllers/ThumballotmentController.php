<?php

namespace app\modules\employee\controllers;

use Yii;
use app\modules\employee\models\EmpInfoThumb;
use app\modules\employee\models\EmpInfoThumbSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;

/**
 * MentorallotmentController implements the CRUD actions for Mentorallotment model.
 */
class ThumballotmentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Mentorallotment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmpInfoThumbSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

 
   


  
    /**
     * Finds the Mentorallotment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mentorallotment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmpInfoThumb::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actions()
   {
       return ArrayHelper::merge(parent::actions(), [
           'studentthumbupdate' => [                                       // identifier for your editable action
               'class' => EditableColumnAction::className(),     // action class name
               'modelClass' => EmpInfoThumb::className(),                // the update model class
               'outputValue' => function ($model, $attribute, $key, $index) {
                    $fmt = Yii::$app->formatter;
                    $value = $model->$attribute;                 // your attribute value
                    if ($attribute === 'stu_thumb') {           // selective validation by attribute
                     if ($value>0){
                        return $value;
                    }
                    else
                    {return '<font color="red"><b>???</b></font>'; }        // return formatted value if desired
                    }
                     //elseif ($attribute === 'publish_date') {   // selective validation by attribute
                       // return $fmt->asDate($value, 'php:Y-m-d');// return formatted value if desired
                    //}
                    return '';                                   // empty is same as $value
               },
               'outputMessage' => function($model, $attribute, $key, $index) {
                     return '';                                  // any custom error after model save
               },
               // 'showModelErrors' => true,                     // show model errors after save
               // 'errorOptions' => ['header' => '']             // error summary HTML options
               // 'postOnly' => true,
               // 'ajaxOnly' => true,
               // 'findModel' => function($id, $action) {},
               // 'checkAccess' => function($action, $model) {}
           ]
       ]);
   }


}
