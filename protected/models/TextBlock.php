<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static TextBlock model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $location
 * @property string $name
 * @property integer $position
 * @property string $url
 * @property integer $visible
 * @property string $content
 * @property string $img
 */
class TextBlock extends FActiveRecord
{
  public $image;

  public function rules()
  {
    return array(
      array('position, visible, auto_created', 'numerical', 'integerOnly' => true),
      array('location, name, url', 'length', 'max' => 255),
      array('content', 'safe'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => "{$alias}.location, {$alias}.position",
    );
  }

  /**
   * @return array
   */
  public function getGroupByLocation()
  {
    $this->dbCriteria->select = 'location, content, id';

    $command = $this->dbConnection->commandBuilder->createFindCommand($this->tableName(), $this->dbCriteria);

    return CHtml::listData($command->queryAll(), 'id', 'content', 'location');
  }

  /**
   * @param string $location
   *
   * @return TextBlock[]|array
   */
  public function getByLocation($location)
  {
    return $this->findAllByAttributes(array('location' => $location));
  }

  public function __toString()
  {
    return $this->content;
  }

  protected function afterFind()
  {
    $this->image = new FSingleImage($this->img, 'textblock');
    parent::afterFind();
  }
}