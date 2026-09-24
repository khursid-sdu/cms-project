<?php
/*****************************************************************************************
 * EduSec  Open Source Edition is a School / College management system developed by
 * RUDRA SOFTECH. Copyright (C) 2010-2015 RUDRA SOFTECH.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses.

 * You can contact RUDRA SOFTECH, 1st floor Geeta Ceramics,
 * Opp. Thakkarnagar BRTS station, Ahmedbad - 382350, India or
 * at email address info@rudrasoftech.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.

 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * RUDRA SOFTECH" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by RUDRA SOFTECH".
 *****************************************************************************************/
/**
 * EmpMasterController implements the CRUD actions for EmpMaster model.
 *
 * @package EduSec.modules.employee.controllers
 */

namespace app\modules\employee\controllers;

use Yii;
use app\modules\employee\models\EmpMaster;
use app\modules\employee\models\EmpMasterSearch;
use app\modules\employee\models\Vacancycreate;
use app\modules\employee\models\Vacancycreaterecommender;
use app\modules\employee\models\VacancycreaterecommenderSearch;
use app\modules\employee\models\VacancycreateSearch;
use app\modules\employee\models\VacancyviewSearch;
use app\modules\employee\models\VacancyreviewSearch;
use app\modules\employee\models\Vacancyrefer;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\employee\models\EmpInfo;
use app\modules\employee\models\EmpAddress;
use app\modules\employee\models\EmpCategory;
use app\modules\employee\models\EmpDepartment;
use app\modules\employee\models\EmpDesignation;
use app\modules\employee\models\EmpStatus;
use app\modules\employee\models\VacancyreferSearch;
use app\modules\employee\models\VacancyreferadminSearch;
use app\models\Nationality;
use app\models\Languages;
use app\models\User;
use app\models\AuthAssignment;
use yii\helpers\Json;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use yii\web\UploadedFile;
use app\modules\employee\models\EmpDocs;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\grid\EditableColumnAction;
class RecruitmentController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'docs-download' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EmpMaster models.
     * @return mixed
     */
     public function actionIndex()
    {   
        return $this->render('index');
    }


     public function actionReport($id)
    {   
        
        if(Yii::$app->user->can('/employee/recruitment/record'))
        {
          $searchModel = new VacancyreferadminSearch();  
        }
        else
        {
         $searchModel = new VacancyreferSearch();   
        }

        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['v_master' => $id]);
        $model = new Vacancyrefer();
        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionResumeDownload( $resume_doc_id )
    {
    $path = Yii::getAlias('@webroot') . '/data/resume_docs/';
    $model = Vacancyrefer::find()->where(['ref_id' => $resume_doc_id])->one();
    $file = $path.$model->resume;
    $ext = substr(strrchr($model->resume,'.'),1);

    if(!empty($model) && file_exists($file)) {
        return Yii::$app->response->sendFile($file, date('Y-m-dHis').".".$ext);
    }
    else
        throw new NotFoundHttpException('The requested page does not exist.');

    }

    /**
     * Displays a single EmpMaster model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        $modelreferral = new Vacancyrefer();
         if ($modelreferral->load(Yii::$app->request->post())) {
        if (Yii::$app->request->isAjax) {
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($modelreferral);
            }
           $modelreferral->attributes = $_POST['Vacancyrefer']; 
           $modelreferral->created_at = new \yii\db\Expression('NOW()');
           $modelreferral->created_by = Yii::$app->getid->getId();
           $modelreferral->resume = UploadedFile::getInstance($modelreferral,'resume');

            if(!empty($modelreferral->resume))  {
                $ext= substr(strrchr($modelreferral->resume, '.'), 1);
                if($ext != null)
                {
                $newFName = $modelreferral->v_master.'-'.mt_rand(1, time()).'.'.$ext;
                }

            }

        $modelreferral->resume=$newFName;
           if($modelreferral->save()){
        $modelreferral->resume = UploadedFile::getInstance($modelreferral,'resume');
        $modelreferral->resume->saveAs(Yii::getAlias('@webroot').'/data/resume_docs/' .$modelreferral->resume=$newFName);
            Yii::$app->session->setFlash('msg', '
     <div class="alert alert-success alert-dismissable">
     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
     <strong>Thanks! </strong> You have referred 1 candidate.</div>'
  );
        }
        else
        {
          Yii::$app->session->setFlash('msg', '
     <div class="alert alert-danger alert-dismissable">
     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
     <strong>Oops! </strong> Seems like this candidate is already refered for the vacancy.</div>'
  );  
        }
        }

        $model = new Vacancycreate();
        $searchModel = new VacancyviewSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_status' => 1]);
       return $this->render('view', [
                      'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                 ]);
    }

public function actionRefer($id)
{
        $modelreferral = new Vacancyrefer();
        return $this->renderAjax('refer', [
            'model' => Vacancycreate::findOne($id), 'modelreferral' => $modelreferral,
        ]);
   
}

    public function actionReview()
    {
        $searchModel = new VacancyreviewSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Vacancycreate();
        return $this->render('review', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

public function actionForward()
    {
        $model = new Vacancycreate();
        $modelx = new Vacancycreaterecommender();
    $searchModel = new VacancyreviewSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($model->load(Yii::$app->request->post())) {
        if (Yii::$app->request->isAjax) {
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
            }

        $model->attributes = $_POST['Vacancycreate'];
        $entry_by_hr=$_POST['Vacancycreate']['entry_by_hr'];
      
        if ($entry_by_hr == '0'){

       $idtoupdate=$_POST['Vacancycreate']['v_id'];

       $modelx->forwarded_by=$_POST['Vacancycreate']['forwarded_to'];
    
       $modelx->vacancy_master_id=$_POST['Vacancycreate']['vacancy_master_id'];
         
       
       $modely = Vacancycreaterecommender::findOne($idtoupdate);

       $modely->forwarded_to = $_POST['Vacancycreate']['forwarded_to'];

       $modely->forwarder_remarks = $_POST['Vacancycreate']['forwarder_remarks'];
       $modely->is_status = 3;
       $modely->created_at = new \yii\db\Expression('NOW()');
      
       

        if($modelx->save() && $modely->save()){
      
            return $this->redirect(['index']);
        }
        else
            return $this->render('index', [
                      'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                 ]);

        }
###
         if ($entry_by_hr == '1'){
       $model->is_status = 1;
       $model->created_at = new \yii\db\Expression('NOW()');
       $model->updated_by = Yii::$app->getid->getId();
       $model->required_date=Yii::$app->formatter->asDate($model->required_date, 'yyyy-MM-dd');
        
        if($model->save()){
           Yii::$app->session->setFlash('msg', '
     <div class="alert alert-success alert-dismissable">
     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
     <strong>Success! </strong> Vacancy application recorded. Details can be viewed from Vacancy Report.</div>'
  );
            return $this->redirect(['index']);
        }
        else
            return $this->render('index', [
                      'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                 ]);

        }

###

        }






        else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

     public function actionRecord($id,$vid)
    {
       
    $model = Vacancycreaterecommender::findOne($id);
    $model->is_status = 1;
    $model->created_at= new \yii\db\Expression('NOW()');
       

    $modeld = Vacancycreate::findOne($vid);
    $modeld->is_status = 1;
    $modeld->updated_at= new \yii\db\Expression('NOW()');
    $modeld->updated_by= Yii::$app->getid->getId();
  if ($model->save() && $modeld->save()){

        return $this->redirect(['index']);}
    }
    /**
     * Creates a new EmpMaster model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    	$model = new Vacancycreate();
        $modelchild = new Vacancycreaterecommender();
        $searchModel = new VacancycreateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($model->load(Yii::$app->request->post())) {
        if (Yii::$app->request->isAjax) {
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
            }

        $model->attributes = $_POST['Vacancycreate'];
        $entry_by_hr=$_POST['Vacancycreate']['entry_by_hr'];
        if ($entry_by_hr == '1'){
          $model->is_status = 1;
          $model->updated_by = Yii::$app->getid->getId();
          $model->eid = Yii::$app->getid->getId();
          $model->created_at= new \yii\db\Expression('NOW()');
          $model->required_date=Yii::$app->formatter->asDate($model->required_date, 'yyyy-MM-dd');
          if($model->save()){
            return $this->redirect(['create']);
        }
        else
            return $this->render('create', [
                      'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                 ]);

        }
        else
        {
          $model->eid = Yii::$app->getid->getId();
        $model->created_at= new \yii\db\Expression('NOW()');
        $model->required_date=Yii::$app->formatter->asDate($model->required_date, 'yyyy-MM-dd');
        

        $modelchild->forwarded_by = $_POST['Vacancycreate']['forwarded_by'];


        if($model->save()){
            $modelchild->vacancy_master_id = $model->vacancy_id;
            $modelchild->save();
            return $this->redirect(['create']);
        }
        else
            return $this->render('create', [
                      'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                 ]);

        }
        
        } else {
            return $this->render('create', [
                'model' => $model,'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing EmpMaster model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModela($id);
        $searchModel = new VacancycreateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($model->load(Yii::$app->request->post())) {
        if (Yii::$app->request->isAjax) {
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ActiveForm::validate($model);
            }
        $model->attributes = $_POST['Vacancycreate'];
        $model->updated_by = Yii::$app->getid->getId();
        $model->updated_at= new \yii\db\Expression('NOW()');
        $model->required_date=Yii::$app->formatter->asDate($model->required_date, 'yyyy-MM-dd');
        
        

        if($model->save())
            return $this->redirect(['create']);
        else
            return $this->render('update', [
                'model' => $model,'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                ]);
            
        } else {
            return $this->render('update', [
                'model' => $model,'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]);
        }
    }



    /**
     * Deletes an existing EmpMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$vid)
    {
       if (($_POST['Vacancycreate']['forwarder_remarks']) =='')
        {
?>
    <script type="text/javascript">
validateMyForm();
function validateMyForm()
{
    alert("Warning!! Can't Disapprove Application. You need to enter your remarks before disapproving.!!");
    returnToPreviousPage();
    return false;
 }

function returnToPreviousPage() {
window.history.back();
}
</script>
    <?php
  exit;
        }
    $model = Vacancycreaterecommender::findOne($id);
    $model->is_status = 2;
    $model->created_at= new \yii\db\Expression('NOW()');
    $model->forwarder_remarks=$_POST['Vacancycreate']['forwarder_remarks'];
   

    $modeld = Vacancycreate::findOne($vid);
    $modeld->is_status = 2;
    $modeld->updated_at= new \yii\db\Expression('NOW()');
    $modeld->updated_by= Yii::$app->getid->getId();
  if ($model->save(false) && $modeld->save(false)){

        return $this->redirect(['index']);}
    }

    /**********************************
	  Employee Document Delete action.
	  emp_doc is the id of emp_docs table.
    *****************/

    public function actionDeleteDoc($emp_doc_id)
    {
    	$emp_docs = EmpDocs::findOne($emp_doc_id);
    	if($emp_docs !== NULL)
            	$emp_docs->delete();
    	else
    		throw new NotFoundHttpException('The requested page does not exist.');

        return $this->redirect(['view', 'id' => $emp_docs->emp_docs_emp_master_id,'#' => 'documents']);
    }


    /**
     * Finds the EmpMaster model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmpMaster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmpMaster::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModela($id)
    {
        if (($model = Vacancycreate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

     public function actions()
   {
       return ArrayHelper::merge(parent::actions(), [
           'advtpublish' => [                                       // identifier for your editable action
               'class' => EditableColumnAction::className(),     // action class name
               'modelClass' => Vacancycreate::className(),                // the update model class
               'outputValue' => function ($model, $attribute, $key, $index) {
                    $fmt = Yii::$app->formatter;
                    $value = $model->$attribute;                 // your attribute value
                    if ($attribute === 'advt_published_at') {           // selective validation by attribute
                     if ($value<>''){
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
           ],

    

       ]);
   }
}
