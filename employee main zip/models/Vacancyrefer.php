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
class Vacancyrefer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    

    public static function tableName()
    {
        return 'vacancy_referral';
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
            [['v_master', 'c_name','c_aadhaar','c_mobile','resume'], 'required','message' => ''],
            [['c_name'], 'string','message' => ''],
            [['ref_id','v_master','c_aadhaar','c_mobile','created_by','updated_by'], 'integer'],
            [['created_at', 'is_status','updated_at','updated_by','created_by'], 'safe'],
            [['is_status'],'default', 'value'=> 0],
            [['v_master', 'c_aadhaar'], 'unique', 'targetAttribute' => ['c_aadhaar'], 'message' => Yii::t('academics', 'Candidate already refered!')],
            [['v_master', 'c_mobile'], 'unique', 'targetAttribute' => ['c_mobile'], 'message' => Yii::t('academics', 'Candidate already refered!')],
           

            
        ];
    }

    /**
     * @inheritdoc
     */

    public function attributeLabels()
    {
        return [
            'ref_id' => Yii::t('academics', 'ID'),
            'v_master' => Yii::t('academics', 'Vacancy ID'),
            'c_name' => Yii::t('academics', 'Candidate Name'),
            'c_aadhaar' => Yii::t('academics', 'Aadhaar No.'),
            'created_at' => Yii::t('academics', 'Created At'),
            'c_mobile' => Yii::t('academics', 'Mobile No.'),
            'is_status' => Yii::t('academics', 'Is Status'),
            'resume' => Yii::t('academics', 'Resume'),
            'created_by' => Yii::t('academics', 'Referred By'),
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
        return $this->hasOne(\app\modules\employee\models\EmpMaster::className(), ['emp_master_user_id' => 'created_by']);
    }
     public function getCheckallOfficer()
    {
        return $this->hasOne(\app\modules\employee\models\Vacancycreaterecommender::className(), ['vacancy_master_id' => 'v_master']);
    }
    
    public function getFacultyxmentorBy()
    {
        return $this->hasOne(\app\modules\employee\models\EmpInfo::className(), ['emp_info_emp_master_id' => 'emp_master_id'])->viaTable('emp_master', ['emp_master_user_id' => 'created_by']//,function ($query) {
            /* @var $query \yii\db\ActiveQuery */

            //$query->andWhere(['subjectteacher_employee_id' => \Yii::$app->user->identity->id]);
        //}
        );
        
    }
    
}
