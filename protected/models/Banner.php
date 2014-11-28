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

  public function findByPage($location = null, $page = null)
  {
    $criteria = new CDbCriteria();

    if( isset($location) )
      $criteria->compare('location', $location);

    $banners = $this->findAll($criteria);

    $page = parse_url($page ? $page : Yii::app()->request->requestUri);

    foreach( $banners as $banner )
    {
      if( !$banner->pagelist )
        continue;

      $exclusionPage = null;

      if( !empty($banner->pagelist_exc) )
      {
        foreach( explode("\n", $banner->pagelist_exc) as $searchPageExc )
        {
          $searchPageExc = str_replace('*', '.+', trim($searchPageExc));
          if( $exclusionPage = preg_match('#^'.$searchPageExc.'$#', $page['path']) )
          {
            break;
          }
        }
      }

      if( !$exclusionPage )
      {
        foreach( explode("\n", $banner->pagelist) as $searchPage )
        {
          $searchPage = str_replace('*', '.+', trim($searchPage));

          if( preg_match('#^'.$searchPage.'$#', $page['path']) )
          {
            return $banner;
          }
        }
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