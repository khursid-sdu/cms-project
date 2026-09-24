<?php

namespace app\modules\employee\models;

use Yii;

/**
 * This is the model class for table "stu_master".
 *
 * @property integer $stu_master_id
 * @property integer $stu_master_stu_info_id
 * @property integer $stu_master_user_id
 * @property integer $stu_master_nationality_id
 * @property integer $stu_master_category_id
 * @property integer $stu_master_course_id
 * @property integer $stu_master_batch_id
 * @property integer $stu_master_section_id
 * @property integer $student_master_house_id
 * @property integer $grop
 * @property string $honors
 * @property integer $vcourse1
 * @property integer $vcourse2
 * @property integer $vcourse3
 * @property string $mentor_id
 * @property integer $stu_master_stu_status_id
 * @property integer $stu_master_stu_address_id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $is_status
 */
class Vacancycreaterecommender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vacancyrecommender';
    }

   

    /**
     * @inheritdoc
     */
  public function rules()
    {
        return [
            [['forwarded_by'], 'required','message' => ''],
            [['forwarder_remarks'], 'string'],
            [['v_id', 'vacancy_master_id','forwarded_by','forwarded_to','is_status'], 'integer'],
            [['created_at','forwarded_to','forwarder_remarks'], 'safe'],
            ];
    }

    /**
     * @inheritdoc
     */

    public function attributeLabels()
    {
        return [
            'v_id' => Yii::t('academics', 'ID'),
            'vacancy_master_id' => Yii::t('academics', 'Vacancy Id'),
            'forwarded_by' => Yii::t('academics', 'Forwarded By'),
            'forwarded_to' => Yii::t('academics', 'Forwarded To'),
            'forwarder_remarks' => Yii::t('academics', 'Remarks'),
            'created_at' => Yii::t('academics', 'Created At'),
            'is_status' => Yii::t('academics', 'Is Status'),
        ];
    }
    public function getCreatedBy()
    {
        return $this->hasOne(\app\models\User::className(), ['forwarded_by' => 'created_by']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultymentorBy()
    {
        return $this->hasOne(\app\modules\employee\models\EmpInfo::className(), ['emp_info_emp_master_id' => 'emp_master_id'])->viaTable('emp_master', ['emp_master_user_id' => 'forwarded_by']//,function ($query) {
            /* @var $query \yii\db\ActiveQuery */

            //$query->andWhere(['subjectteacher_employee_id' => \Yii::$app->user->identity->id]);
        //}
        );
        
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultymentorxBy()
    {
        return $this->hasOne(\app\modules\employee\models\EmpInfo::className(), ['emp_info_emp_master_id' => 'emp_master_id'])->viaTable('emp_master', ['emp_master_user_id' => 'forwarded_to']//,function ($query) {
            /* @var $query \yii\db\ActiveQuery */

            //$query->andWhere(['subjectteacher_employee_id' => \Yii::$app->user->identity->id]);
        //}
        );
        
    }
    
}

