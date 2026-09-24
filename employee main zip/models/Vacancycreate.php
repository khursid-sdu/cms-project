<?php

namespace app\modules\employee\models;

use Yii;

/**
 * This is the model class for table "courseoutcome".
 *
 * @property integer $co_id
 * @property string $cotype
 * @property integer $co_subject_id
 * @property string $codescription
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $is_status
 */
class Vacancycreate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $forwarded_by,$forwarded_to,$forwarder_remarks,$v_id,$entry_by_hr,$candidate_name,$candidate_mobile_number,$candidate_aadhaar_number;

    public static function tableName()
    {
        return 'vacancycreate';
    }
   // public static function find()
    //{
      //  return parent::find()->andWhere(['<>', 'leave.is_status', 2]);
    //}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vacancy_jobrole', 'vacancy_desired_qualification','required_date','noofvacancies'], 'required','message' => ''],
            [['vacancy_desired_qualification','advt_published_at'], 'string','message' => ''],
            [['vacancy_jobrole','is_status','eid','vacancy_id','updated_by','noofvacancies'], 'integer'],
            [['created_at', 'is_status','eid','updated_at','updated_by','advt_published_at'], 'safe'],
            [['noofvacancies'],'default', 'value'=> 0],
           
            
        ];
    }

    /**
     * @inheritdoc
     */

    public function attributeLabels()
    {
        return [
            'vacancy_id' => Yii::t('academics', 'ID'),
            'eid' => Yii::t('academics', 'Employee'),
            'vacancy_jobrole' => Yii::t('academics', 'Vacancy Designation'),
            'required_date' => Yii::t('academics', 'Required Date'),
            'created_at' => Yii::t('academics', 'Created At'),
            'vacancy_desired_qualification' => Yii::t('academics', 'Desired Qualification'),
            'is_status' => Yii::t('academics', 'Is Status'),
            'noofvacancies' => Yii::t('academics', 'Persons Required'),
            'advt_published_at' => Yii::t('academics', 'Advertisement Published in'),
        ];
    }
    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['user_id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['user_id' => 'updated_by']);
    }
    public function getCheckOfficer()
    {
        return $this->hasOne(\app\modules\employee\models\EmpMaster::className(), ['emp_master_user_id' => 'eid']);
    }
     public function getCheckallOfficer()
    {
        return $this->hasOne(\app\modules\employee\models\Vacancycreaterecommender::className(), ['vacancy_master_id' => 'vacancy_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpMasterDesignation()
    {
        return $this->hasOne(\app\modules\employee\models\EmpDesignation::className(), ['emp_designation_id' => 'vacancy_jobrole']);
    }
    public function getFacultyxmentorBy()
    {
        return $this->hasOne(\app\modules\employee\models\EmpInfo::className(), ['emp_info_emp_master_id' => 'emp_master_id'])->viaTable('emp_master', ['emp_master_user_id' => 'eid']//,function ($query) {
            /* @var $query \yii\db\ActiveQuery */

            //$query->andWhere(['subjectteacher_employee_id' => \Yii::$app->user->identity->id]);
        //}
        );
        
    }
    
}
