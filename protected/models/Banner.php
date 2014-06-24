<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static Banner model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property integer $location
 * @property string $title
 * @property string $url
 * @property string $img
 * @property integer $swd_w
 * @property integer $swd_h
 * @property string $code
 * @property string $pagelist
 * @property string $pagelist_exc
 * @property boolean $new_window
 * @property boolean $visible
 */
class Banner extends FActiveRecord
{
  public $image;

  protected $banners = array();

  public function tableName()
  {
    return '{{banner}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }

  public function getByLocation($location)
  {
    if( !isset($this->banners[$location]) )
      $this->banners[$location] = $this->findAllByAttributes(array('location' => $location));

    return $this->banners[$location];
  }

  public function findByPage($page, $location = null)
  {
    $criteria = new CDbCriteria();

    if( isset($location) )
      $criteria->compare('location', $location);

    $banners = $this->findAll($criteria);

    foreach($banners as $banner)
    {
      if( !$banner->pagelist )
        continue;

      foreach(explode("\n", $banner->pagelist) as $searchPage)
      {
        if( trim($searchPage) == $page )
          return $banner;
      }
    }

    return null;
  }

  protected function afterFind()
  {
    $this->image = $this->img ? new FSingleImage($this->img, 'images') : null;
    parent::afterFind();
  }
}