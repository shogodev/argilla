<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
 *
 * @method static MetaMask model(string $class = __CLASS__)
 *
 * @property string $url_mask
 * @property string $header
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property integer $noindex
 * @property integer $visible
 */
class MetaMask extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_meta_mask}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  /**
   * @param $url
   *
   * @return MetaMask
   */
  public function findByUri($url)
  {
    $url = Utils::normalizeUrl($url);

    if( !$model = $this->findByAttributes(array('url_mask' => $url)) )
    {
      $model = $this->findByMask($url);
    }

    return $model;
  }

  /**
   * @param $url
   *
   * @return MetaMask
   */
  private function findByMask($url)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('url_mask', '#', true);

    foreach($this->findAll($criteria) as $mask)
      if( @preg_match($mask['url_mask'], $url) )
        return $mask;
  }
}