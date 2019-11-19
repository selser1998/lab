<?php
/**
 * Модель для работы с жанрами
 * @author Vladimir Statsenko
 */
class Types extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ygs_types':
	 * @var integer $t_id
	 * @var string $t_name
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ygs_types';
	}

	/**
	 * @return array правила для валидации формы.
	 */
	public function rules()
	{
		return array(
			array('t_name','length','max'=>45),
			array('t_id', 'required'),
			array('t_id', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array связи между таблицей жанров и игр.
	 */
	public function relations()
	{
		return array(
			'ygs_games' => array(self::MANY_MANY, 'Games', 'ygs_games_types(gt_game_id, gt_type_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			't_id' => 'Id жанра',
			't_name' => 'Жанр',
		);
	}
	
	public function safeAttributes() {
		return array('t_id', 't_name');
	}
}