<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property integer $id
 * @property integer $position
 * @property integer $page
 * @property integer $section_id
 * @property string $date
 * @property string $url
 * @property string $title
 * @property string $content
 * @property string $region
 * @property string $email
 * @property integer $visible
 * @property LinkSection $section
 *
 * @method static Link model(string $link = __CLASS__)
 */
class Link extends FActiveRecord
{
	public function tableName()
	{
		return '{{seo_link}}';
	}

	public function rules()
	{
		return array(
			array('title, url', 'required'),
			array('title, url, region', 'length', 'max' => 255, 'min' => 3),
			array('url', 'url'),
			array('email', 'email'),
			array('section_id', 'numerical', 'integerOnly' => true)
		);
	}

	public function relations()
	{
		return array(
			'section' => array(self::BELONGS_TO, 'LinkSection', 'section_id'),
		);
	}

	public function defaultScope()
	{
		return array(
			'condition' => '`visible` = 1',
			'order' => '`position` ASC',
		);
	}

	public function beforeSave()
	{
		if( parent::beforeSave() )
		{
			$this->date = new CDbExpression('NOW()');
			$this->visible = 0;

			return true;
		}

		return false;
	}

	public function afterFind()
	{
		parent::afterFind();

		$this->date = date('d.m.Y', strtotime($this->date));
		$this->url = Yii::app()->createUrl('link/one', array('id' => $this->id));
	}
}