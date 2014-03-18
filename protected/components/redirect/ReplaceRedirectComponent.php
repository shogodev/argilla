<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ReplaceRedirectComponent extends FRedirectComponent
{
  /**
   * @var
   */
  private $staticPatterns;

  /**
   * @param $url
   *
   * @return bool
   */
  public function hasStaticPatterns($url)
  {
    foreach(array_keys($this->staticPatterns) as $pattern)
    {
      if( strpos($url, $pattern) === 0 )
      {
        return true;
      }
    }

    return false;
  }

  public function init()
  {
    $this->criteria = new CDbCriteria();
    $this->criteria->compare('type_id', RedirectHelper::TYPE_REPLACE);
    $this->criteria->compare('visible', 1);

    $this->initStaticPatterns();
    parent::init();

  }

  /**
   * @param string $url
   *
   * @return string
   */
  public function getUrl($url)
  {
    if( $data = $this->findByKey($url) )
    {
      return $data['target'];
    }
    elseif( $data = $this->findByPattern($url) )
    {
      return $data['target'];
    }

    return $url;
  }

  /**
   * @param string $url
   *
   * @return string
   */
  public function getStaticUrl($url)
  {
    return $this->findByStaticPattern($url);
  }

  private function findByStaticPattern($url)
  {
    foreach($this->staticPatterns as $pattern => $callback)
    {
      if( strpos($url, $pattern) === 0 )
      {
        $url = $callback($url);
      }
    }

    return $url;
  }

  private function initStaticPatterns()
  {
    $this->staticPatterns['{HTTP_HOST}'] = function($url) {
      return str_replace('{HTTP_HOST}', Yii::app()->getRequest()->getHostInfo(), $url);
    };
    $this->staticPatterns['http://'] = function($url) {
      return $url;
    };
  }
}
