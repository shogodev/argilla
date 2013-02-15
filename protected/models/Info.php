<?php
/**
 * @property string  $id
 * @property string  $date
 * @property integer $position
 * @property string  $template
 * @property string  $name
 * @property string  $url
 * @property string  $img
 * @property string  $notice
 * @property string  $content
 * @property string  $reference
 * @property string  $visible
 * @property string  $siblings
 * @property string  $children
 * @property string  $menu
 * @property string  $sitemap
 * @property int     $comments_are_allowed
 */
class Info extends FActiveRecord implements IMenuItem
{
  const ROOT_ID = 1;

  const PATH = 'f/info/';

  public function tableName()
  {
    return '{{info}}';
  }

  public function behaviors()
  {
    return array('nestedSetBehavior' => array('class' => 'nestedset.NestedSetBehavior'));
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function defaultScope()
  {
    return array(
      'condition' => '`visible` = 1',
    );
  }

  public function scopes()
  {
    return array(
      'visible' => array(
        'condition' => 'visible=1'
      ),
      'withNotice' => array(
        'condition' => 'notice!=""',
      ),
      'main' => array(
        'condition' => 'main=1',
      ),
    );
  }

  /**
   * @return bool
   */
  public function areCommentsAllowed()
  {
    return (bool) $this->comments_are_allowed;
  }

  public function getMenu(CDbCriteria $criteria = null)
  {
    if( !$criteria )
      $criteria = new CDbCriteria();

    $root = $this->getRoot();

    $criteria->order = $this->leftAttribute;
    $criteria->compare('menu', '=1');
    $criteria->compare('visible', '=1');

    $rawTree = $root->descendants()->findAll($criteria);
    $tree    = $this->buildTree($rawTree, array($this, 'buildMenuItem'), array(), 'items');

    return $tree;
  }

  public function getRoot()
  {
    $parents = $this->getParents();
    return $parents ? reset($parents) : $this;
  }

  public function getParents()
  {
    return $this->ancestors()->visible()->findAll();
  }

  public function getDescendants($depth = 1)
  {
    return $this->descendants($depth)->visible()->findAll();
  }

  public function getSiblings($includeNode = false)
  {
    $criteria = new CDbCriteria();
    if( !$includeNode )
      $criteria->compare('id', '<>'.$this->getPrimaryKey());

    $parent   = $this->parent()->find();
    $siblings = $parent->children()->findAll($criteria);

    return $siblings;
  }

  public function getBreadcrumbs()
  {
    $breadcrumbs = array();

    foreach($this->parents as $parent)
      $breadcrumbs[$parent->name] = array('info/index', 'url' => $parent->url, 'items' => $this->getRelationItems('info/index', $parent->parent()->find(), $parent->id));

    $breadcrumbs[$this->name] = array('info/index', 'url' => $this->url, 'items' => $this->getRelationItems('info/index', $this->parent()->find(), $this->id));

    return $breadcrumbs;
  }

  public function getRelationItems($route, $model = null, $exceptionId = null)
  {
    if( empty($model) )
      $model = $this;

    $relationItems = empty($exceptionId) ? $model->children()->visible()->findAll() : $model->children()->visible()->findAll('id != :id', array(':id' => $exceptionId));

    $items = array();
    foreach($relationItems as $item)
      $items[] = array('label' => $item->name, 'url' => array($route, 'url' => $item->url));

    return $items;
  }

  public function getTemplate()
  {
    $template = 'info';
    $parents  = $this->getParents();

    foreach($parents as $parent)
      $template = !empty($parent->template) ? $parent->template : $template;

    return '/info/'.(!empty($this->template) ? $this->template : $template);
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

  public function getName()
  {
    return $this->name;
  }

  public function getChildren()
  {
    return array();
  }

  public function getMenuLink()
  {
    return array('info/index', 'url' => $this->url);
  }

  public function setDepth($d)
  {

  }

  protected function afterFind()
  {
    $this->img = $this->img ? self::PATH.$this->img : '';

    parent::afterFind();
  }
}