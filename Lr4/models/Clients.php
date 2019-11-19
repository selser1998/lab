<?php

class Clients extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ygs_clients':
	 * @var integer $c_id
	 * @var string $c_name
	 * @var string $c_email
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
		return 'ygs_clients';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('c_name','length','max'=>45),
			array('c_email','length','max'=>150),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'c_id' => 'C',
			'c_name' => 'C Name',
			'c_email' => 'C Email',
		);
	}
}