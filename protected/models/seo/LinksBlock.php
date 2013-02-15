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
class LinksBlock extends FActiveRecord
  {
  public function tableName()
  {
    return '{{seo_links_block}}';
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
    $charList   = " \r\n/";
    $copyrights = array();
    $url        = trim($url, $charList);

    $criteria = new CDbCriteria();
    $criteria->compare('`key`', '='.$key);

    foreach(self::model()->findAll($criteria) as $copyright)
      foreach(explode("\r", $copyright->url) as $page)
        $copyrights[trim($page, $charList)][] = $copyright;

    if( isset($copyrights[$url]) )
      $copyrights = $copyrights[$url];
    else if( isset($copyrights['*']) )
      $copyrights = $copyrights['*'];
    else
      $copyrights = array();

    return $copyrights;
  }
}