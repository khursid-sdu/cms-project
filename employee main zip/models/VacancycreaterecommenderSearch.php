<?php

namespace app\modules\employee\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\employee\models\Vacancycreaterecommender;

/**
 * MentorallotmentSearch represents the model behind the search form about `app\modules\mentor\models\Mentorallotment`.
 */
class VacancycreaterecommenderSearch extends Vacancycreaterecommender
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
          return [
            [['forwarder_remarks'], 'string'],
            [['v_id', 'vacancy_master_id','forwarded_by','forwarded_to','is_status'], 'integer'],
            [['created_at'], 'safe'],
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
        $query = Vacancycreaterecommender::find();

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
            'forwarder_remarks' => $this->forwarder_remarks,
            'v_id' => $this->v_id,
            'vacancy_master_id' => $this->vacancy_master_id,
            'forwarded_by' => $this->forwarded_by,
            'forwarded_to' => $this->forwarded_to,
            'is_status' => $this->is_status,
            'created_at' => $this->created_at,
            
            ]);

       

        return $dataProvider;
    }
}
