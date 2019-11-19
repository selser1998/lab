<?php

class Games extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'ygs_games':
	 * @var integer $g_id
	 * @var integer $g_rate
	 * @var string $g_name_url
	 * @var integer $g_type
	 * @var string $g_added
	 * @var integer $g_size
	 * @var string $g_name
	 * @var string $g_medium_pic
	 * @var string $g_small_pic
	 * @var string $g_download_link
	 * @var string $g_shortdescr
	 * @var string $g_fulldescr
	 * @var string $g_publish_date
	 * @var integer $g_state
	 */
	
	//эти два свойства используются при импорте игр
	public $g_screenshots = array();
	public $g_types = array();
	//это свойство используется для того, чтобы отключить удаление
	//скриншотов при обновлении страницы, т.к. форма обновления не
	//содержит списка скриншотов
	public $updateScreenshots = true;

	const PUBLISHED = 0;
	const DRAFT = 1;

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
		return 'ygs_games';
	}

	/**
	 * @return array правила валидации данных (при обновлении игры).
	 */
	public function rules()
	{
		return array(
			array('g_name_url','length','max'=>255),
			array('g_name','length','max'=>255),
			array('g_medium_pic','length','max'=>255),
			array('g_small_pic','length','max'=>255),
			array('g_download_link','length','max'=>255),
			array('g_id, g_rate, g_name_url, g_type, g_added, g_size, g_name, g_medium_pic, g_small_pic, g_download_link, g_state', 'required'),
			array('g_id, g_rate, g_type, g_size, g_state', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			//указываем, что в результам запроса нужно включить данные из связанной таблицы
			'ygs_types' => array(self::MANY_MANY, 'Types', 'ygs_games_types(gt_game_id, gt_type_id)','together'=>true,'joinType'=>'INNER JOIN'),
			'ygs_screenshots' => array(self::HAS_MANY, 'Screenshots', 's_game_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'g_id' => 'ID',
			'g_rate' => 'Рейтинг',
			'g_name_url' => 'Короткая ссылка',
			'g_type' => 'Жанр',
			'g_added' => 'Дата добавления',
			'g_size' => 'Размер (кБ)',
			'g_name' => 'Название',
			'g_medium_pic' => 'Картинка (средний размер)',
			'g_small_pic' => 'Миниатюра',
			'g_download_link' => 'Ссылка',
			'g_shortdescr' => 'Короткое описание',
			'g_fulldescr' => 'Полное описание',
			'g_publish_date' => 'Дата публикации',
			'g_state' => 'Статус',
		);
	}

	/**
	 * Сохраняет скриншоты и связи между играми и жанрами после сохранения
	 * игры
	 */
	public function afterSave() {
		//если запись уже существует, то перед обновлением удаляем все связанные данные
		if (!$this->isNewRecord) {
			$this->dbConnection->createCommand('DELETE FROM ygs_games_types WHERE gt_game_id='.$this->g_id)->execute();
			//скриншоты удаляем только если updateScreenshots == true
			if ($this->updateScreenshots) {
				$this->dbConnection->createCommand('DELETE FROM ygs_screenshots WHERE s_game_id='.$this->g_id)->execute();
			}
		}
		//сохраняем жанры
		foreach ($this->g_types as $type) {
			if (($t = Types::model()->findByPk($type)) !== null) {
				$this->dbConnection->createCommand('INSERT INTO ygs_games_types (gt_game_id, gt_type_id) VALUES ('.$this->g_id.','.$type.')')->execute();
			}
		}
		//сохраняем скриншоты
		if ($this->updateScreenshots) {
			$command = $this->dbConnection->createCommand('INSERT INTO ygs_screenshots (s_game_id, s_image, s_thumbnail) VALUES (:s_game_id, :s_image, :s_thumbnail)');
			foreach ($this->g_screenshots as $screenshot) {
				$command->bindParam(':s_game_id', $this->g_id, PDO::PARAM_INT);
				$command->bindParam(':s_image', $screenshot->IMAGE, PDO::PARAM_STR);
				$command->bindParam(':s_thumbnail', $screenshot->THUMBNAIL, PDO::PARAM_STR);
				$command->execute();
			}
		}
	}
	
	/**
	 * Массив с атрибутами игры, которые можно читать
	 * @return array массив с безопасными атрибутами
	 */
	public function safeAttributes() {
		return array('g_id', 'g_rate', 'g_name_url', 'g_type',
			'g_added', 'g_size', 'g_name', 'g_medium_pic',
			'g_small_pic', 'g_download_link', 'g_shortdescr',
			'g_fulldescr', 'g_publish_date', 'g_state', 'g_types',
			'g_screenshots'
		);
	}
	
	/**
	 * Возвращает массив с состояниями игры (черновик/опубликовано)
	 * @return array массив с состояними
	 */
	public function getStatusOptions()
	{
		return array(
			self::DRAFT=>'Черновик',
			self::PUBLISHED=>'Опубликовано',
		);
	}
	
	/**
	 * возвращает описание статуса
	 * @return string описание статуса
	 */
	public function getStatusText($status) {
		$options = $this->getStatusOptions();
		return $options[$status];
	}
	
	/**
	 * Возвращает id всех игр, записанных в БД
	 * @return array массив с id сохраненных игр
	 */
	public function getExistingIds() {
		$res = $this->dbConnection->createCommand('SELECT g_id FROM '.$this->tableName())->queryAll();
		$ids = array();
		foreach ($res as $id) {
			$ids[] = $id['g_id'];
		}
		return $ids;
	}
	
	/**
	 * Вставляет условия, которые используются для создания списка лучших игр
	 * @param $params массив с параметрами поиска
	 * @return Games модель с измененными условиями
	 */
	public function getTopGames($params) {
		$this->getDbCriteria()->mergeWith($params);
		return $this;
	}
	
	/**
	 * Возвращает массив методов, которые можно испольлзовать при поиске игр.
	 * Например, если есть элемент 'published', то можно использовать
	 * Games::model()->published()->...
	 * @return array массив с именами методов и условиями поиска
	 */
	public function scopes() {
		return array(
			'published'=>array(
				'condition'=>'g_state='.self::PUBLISHED,
			)
		);
	}
}