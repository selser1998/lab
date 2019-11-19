<?php
/**
 * Модель для работы со скриншотами
 * 
 * @author Vladimir Statsenko
 */
class Screenshots extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ygs_screenshots':
	 * @var integer $s_id
	 * @var integer $s_game_id
	 * @var string $s_image
	 * @var string $s_thumbnail
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
		return 'ygs_screenshots';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('s_image','length','max'=>255),
			array('s_thumbnail','length','max'=>255),
			array('s_image, s_thumbnail', 'required'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			//добавляем перечень полей, которые должны быть в SELECT части запроса
			's_game' => array(self::BELONGS_TO, 'Games', 's_game_id', 'select'=>'g_id, g_name'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			's_id' => 'S',
			's_game_id' => 'S Game',
			's_image' => 'S Image',
			's_thumbnail' => 'S Thumbnail',
		);
	}
}