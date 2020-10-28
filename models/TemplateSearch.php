<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Template;

/**
 * SubdivisionSearch represents the model behind the search form about `app\models\Subdivision`.
 */
class TemplateSearch extends Template {

    public $specIds = [];
    public $docId = null;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'safe'],
            [['deleted'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Template::find()
            ->joinWith('templateDocs')
            ->joinWith('templateSpecs');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 999
            ]
        ]);

        //sort
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'name',
                'spec_id',
                'doctor_id'
            ],
            'defaultOrder' => [
                'name' => SORT_ASC
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.draft' => false,
            self::tableName() . '.deleted' => false,
            self::tableName() . '.branch_id' => Yii::$app->user->identity->branch_id
        ]);

        if ($this->specIds || $this->docId) {
            $query->andWhere([
                    'OR',
                    [TemplateSpec::tableName() . '.spec_id' => $this->specIds],
                    [TemplateDoc::tableName() . '.doc_id' => $this->docId]
                ]
            );
        }
        $query->andFilterWhere(['ilike', self::tableName() . '.name', $this->name]);

        return $dataProvider;
    }

}
