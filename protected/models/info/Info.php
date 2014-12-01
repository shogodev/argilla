<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.info
 *
 * @method static Info model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string  $date
 * @property integer $position
 * @property string  $template
 * @property string  $name
 * @property string  $url
 * @property string  $notice
 * @property string  $content
 * @property string  $reference
 * @property integer $visible
 * @property integer $siblings
 * @property integer $children
 * @property integer $menu
 * @property integer $sitemap
 *
 * @property integer leftAttribute
 * @mixin NestedSetBehavior
 */
class Info extends FActiveRecord implements IMenuItem
{
  const ROOT_ID = 1;

  protected $files;

  public function behaviors()
  {
    return array('nestedSetBehavior' => array('class' => 'nestedset.NestedSetBehavior'));
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  public function getMenu(CDbCriteria $criteria = null)
  {
    if( !$criteria )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('menu', '=1');
    }

    $criteria->order = $this->leftAttribute;
    $criteria->compare('visible', '=1');

    $rawTree = $this->getRoot()->descendants()->findAll($criteria);

    return $this->buildTree($rawTree, array($this, 'buildMenuItem'), array(), 'items');
  }

  public function getRoot()
  {
    $parents = $this->getParents();
    return $parents ? reset($parents) : $this;
  }

  public function getParents()
  {
    return $this->ancestors()->findAll();
  }

  /**
   * Получаем дочерние элементы модели
   *
   * @param int $depth
   *
   * @return mixed
   */
  public function getDescendants($depth = 1)
  {
    return $this->descendants($depth)->findAll();
  }

  public function getSiblings($includeNode = false)
  {
    $criteria = new CDbCriteria();
    $criteria->addCondition(array('siblings' => 1));

    if( !$includeNode )
      $criteria->compare('id', '<>'.$this->getPrimaryKey());

    $parent = $this->resetScope()->parent()->find();

    return $parent ? $parent->children()->findAll($criteria) : array();
  }

  public function getTemplate()
  {
    $template = 'index';
    $parents  = $this->getParents();

    foreach($parents as $parent)
      $template = !empty($parent->template) ? $parent->template : $template;

    return (!empty($this->template) ? $this->template : $template);
  }

  public function buildMenuItem($node)
  {
    return array(
      'id'          => 'node_'.$node->id,
      'label'       => $node->name,
      'url'         => array('info/index', 'url' => $node->url),
      'level'       => $node->level,
      'node'        => $node,
      'items'       => array(),
      'htmlOptions' => array('class' => ''),
    );
  }

  /**
   * @param string $type
   *
   * @return InfoFiles
   */
  public function getFile($type = 'main')
  {
    return InfoFiles::model()->findByAttributes(
      array(
        'type' => $type,
        'parent' => $this->id,
      ),
      array(
        'order' => 'IF(position, position, 999999999)'
      ));
  }

  /**
   * @param string $type
   *
   * @return InfoFiles[]
   */
  public function getFiles($type = 'main')
  {
    if( empty($this->files) )
    {
      $files = InfoFiles::model()->findAllByAttributes(
        array('parent' => $this->id),
        array('order' => 'IF(position, position, 999999999)')
      );

      $this->setFiles($files, $type);
    }

    return isset($this->files[$type]) ? $this->files[$type] : array();
  }

  /**
   * @param array $files
   * @param string $type
   *
   * @return void
   */
  public function setFiles($files, $type)
  {
    if( !isset($this->files[$type]) )
      $this->files[$type] = array();

    foreach($files as $file)
      $this->files[$file['type']][] = $file;
  }

  public function getBreadcrumbs()
  {
    $breadcrumbs = array();

    foreach($this->getParents() as $parent)
      $breadcrumbs[$parent->name] = array('info/index', 'url' => $parent->url);

    $breadcrumbs[] = $this->name;

    return $breadcrumbs;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getChildren()
  {
    return array();
  }

  public function getMenuUrl()
  {
    return array('info/index', 'url' => $this->url);
  }

  public function setDepth($depth = null)
  {

  }
}