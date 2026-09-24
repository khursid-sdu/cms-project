<?php

namespace app\modules\employee\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\Vacancycreate;

/**
 * CourseoutcomeSearch represents the model behind the search form about `app\modules\academics\models\Courseoutcome`.
 */
class VacancycreateSearch extends Vacancycreate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
         return [
            [['vacancy_desired_qualification'], 'string','message' => ''],
            [['is_status','eid','vacancy_id','updated_by','vacancy_jobrole','noofvacancies'], 'integer'],
            [['created_at','updated_at', 'is_status','eid','required_date','advt_published_at'], 'safe'],
            
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
        $query = Vacancycreate::find()->orderBy(['is_status' => SORT_ASC]);

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
            'vacancy_id' => $this->vacancy_id,
            'eid' => \Yii::$app->getid->getId(),
            'vacancy_jobrole' => $this->vacancy_jobrole,
            'created_at' => $this->created_at,
            'vacancy_desired_qualification' => $this->vacancy_desired_qualification,
            'is_status' => $this->is_status,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'noofvacancies' => $this->noofvacancies,
            ]);

        $query->andFilterWhere(['like', 'required_date', $this->dbDateSearch($this->required_date)])
              ->andFilterWhere(['like', 'advt_published_at', $this->dbDateSearch($this->advt_published_at)]);

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
