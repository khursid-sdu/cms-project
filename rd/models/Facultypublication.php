<?php

namespace app\modules\rd\models;

use yii\db\ActiveRecord;

/**
 * Facultypublication model
 *
 * @property int $id
 * @property int $faculty_name
 * @property string $paper_title
 * @property string $name_of
 * @property string $volume
 * @property string $issue
 * @property int $pub_month
 * @property int $pub_year
 */
class Facultypublication extends ActiveRecord
{
    public static function tableName()
    {
        return 'facultypublication';
    }

    public function rules()
    {
        return [
            [['faculty_name', 'paper_title', 'name_of'], 'required'],
            [['faculty_name', 'pub_month', 'pub_year'], 'integer'],
            [['paper_title', 'name_of'], 'string', 'max' => 255],
            [['volume', 'issue'], 'string', 'max' => 50],
        ];
    }
}
