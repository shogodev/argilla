<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.sitemap
 *
 * @method static SitemapExclusion model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string  $route
 * @property bool    $lastmod
 * @property string  $changefreq
 * @property float   $priority
 */
class SitemapExclusion extends FActiveRecord
{
  private $fullItems = array();
  private $useItems = array();

  public function tableName()
  {
    return '{{seo_sitemap_exclusion}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(true, false);

    return array(
      'condition' => "{$alias}.visible = 1",
    );
  }

  public function setFullExclusion()
  {
    $data = $this->findAll();

    foreach($data as $value)
    {
      $value['route'] = Yii::app()->getBaseUrl(true).$value['route'];
      $this->fullItems[$value->id] = $value;
    }
  }

  /**
   * @param $url
   *
   * @return SitemapExclusion
   */
  public function getExclusion($url)
  {
    foreach($this->fullItems as $value)
    {
      if( $value->route == $url )
      {
        //добавляем для последующего удаления из общего массива
        $this->useItems[$value->id] = $value->id;
        return $value;
      }
    }

    return null;
  }

  /**
   * @return SitemapExclusion[]
   */
  public function getOtherExclusion()
  {
    return array_diff_key($this->fullItems, $this->useItems);
  }
}