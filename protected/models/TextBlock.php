<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
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

  public function tableName()
  {
    return '{{text_block}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
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