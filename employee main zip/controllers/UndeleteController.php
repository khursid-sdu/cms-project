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
use app\modules\employee\models\EmpMasterundelete;
use app\modules\employee\models\EmpMasterundeleteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\employee\models\EmpInfo;
use app\modules\employee\models\EmpAddress;
use app\modules\employee\models\EmpCategory;
use app\modules\employee\models\EmpDepartment;
use app\modules\employee\models\EmpDesignation;
use app\modules\employee\models\EmpStatus;
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

class UndeleteController extends Controller
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
        $searchModel = new EmpMasterundeleteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    /**
     * Deletes an existing EmpMaster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUndelete($id)
    {
       // $this->findModel($id)->delete();
    	$model = EmpMasterundelete::findOne($id);
    	$model->is_status = 0;
    	$model->emp_master_status_id = 1;
    	$model->updated_by = Yii::$app->getid->getId();
    	$model->updated_at = new \yii\db\Expression('NOW()');
    	if ($model->update())
    {
        Yii::$app->session->setFlash('msg', '
     <div class="alert alert-success alert-dismissable">
     <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
     <strong>Success! </strong> Employee Restored.</div>'
  );
    }

        return $this->redirect(['index']);
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
        if (($model = EmpMasterundelete::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
