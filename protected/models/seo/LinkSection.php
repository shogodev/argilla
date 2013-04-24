<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $position
 * @property int $visible
 * @property Link[] $links
 *
 * @method static LinkSection model(string $class = __CLASS__)
 */
class LinkSection extends FActiveRecord
{
	public function tableName()
	{
		return '{{seo_link_section}}';
	}

	public function relations()
	{
		return array(
			'links' => array(self::HAS_MANY, 'BLink', 'section_id'),
		);
	}

	public function defaultScope()
	{
		return array(
			'condition' => '`visible` = 1',
		);
	}
}