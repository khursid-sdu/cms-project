<?php

namespace app\modules\employee\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\Vacancyrefer;

/**
 * CourseoutcomeSearch represents the model behind the search form about `app\modules\academics\models\Courseoutcome`.
 */
class VacancyreferSearch extends Vacancyrefer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
         return [
            
            [['c_name'], 'string'],
            [['ref_id','v_master','c_aadhaar','c_mobile','created_by','updated_by','is_status'], 'integer'],
            [['created_at','updated_at','updated_by','created_by'], 'safe'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Vacancyrefer::find()->orderBy(['c_name' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ref_id' => $this->ref_id,
            'created_by' => \Yii::$app->getid->getId(),
            'v_master' => $this->v_master,
            'c_aadhaar' => $this->c_aadhaar,
            'c_mobile' => $this->c_mobile,
            'is_status' => $this->is_status,
            'updated_by' => $this->updated_by,
            ]);

        $query->andFilterWhere(['like', 'c_name', $this->c_name])
              ->andFilterWhere(['like', 'created_at', $this->dbDateSearch($this->created_at)]);

        return $dataProvider;
    }

      private function dbDateSearch($value)
    {
            if($value != "" && preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $value,$matches))
                return date("Y-m-d",strtotime($matches[0]));
            else
                return $value;
    }
}
