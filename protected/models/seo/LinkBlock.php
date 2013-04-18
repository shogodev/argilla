<?php
/**
 * @property string  $id
 * @property integer $position
 * @property string  $name
 * @property string  $code
 * @property string  $key
 * @property string  $url
 * @property integer $visible
 */
class LinkBlock extends FActiveRecord
  {
  public function tableName()
  {
    return '{{seo_link_block}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }

  public function afterFind()
  {
    $this->code = str_replace("{Y}", date("Y"), $this->code);
    parent::afterFind();
  }

  public function __toString()
  {
    return $this->code;
  }

  /**
   * @param string $key
   * @param string $url
   *
   * @return array
   */
  public function getLinks($key = 'copyright', $url)
  {
    $copyrights = array();
    $url        = trim($url, "/");

    $criteria = new CDbCriteria();
    $criteria->compare('`key`', $key);

    foreach($this->findAll($criteria) as $copyright)
      foreach(explode("\r", trim($copyright->url)) as $page)
        $copyrights[trim($page, "/")][] = $copyright;

    if( isset($copyrights[$url]) )
      $copyrights = CMap::mergeArray($copyrights[$url], $copyrights['*']);
    else if( isset($copyrights['*']) )
      $copyrights = $copyrights['*'];
    else
      $copyrights = array();

    return $copyrights;
  }
}