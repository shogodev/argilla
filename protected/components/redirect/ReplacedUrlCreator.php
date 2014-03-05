<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ReplacedUrlCreator extends CComponent
{
  /**
   * @var array
   */
  private $replacedUrls = array();

  /**
   * @var array
   */
  private $replacedPatterns = array();

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
    $this->initReplacedUrls();
    $this->initStaticPatterns();
  }

  /**
   * @param string $url
   *
   * @return string
   */
  public function getUrl($url)
  {
    if( $replacedUrl = $this->findByKey($url) )
    {
      return $replacedUrl;
    }
    elseif( $replacedUrl = $this->findByPattern($url) )
    {
      return $replacedUrl;
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

  /**
   * @param string $url
   *
   * @return string|null
   */
  private function findByKey($url)
  {
    return Arr::get($this->replacedUrls, $url);
  }

  /**
   * @param string $url
   *
   * @return string|null
   */
  private function findByPattern($url)
  {
    foreach($this->replacedPatterns as $pattern => $target)
    {
      if( @preg_match($pattern, $url) )
      {
        return preg_replace($pattern, $target, $url);
      }
    }

    return null;
  }

  private function initReplacedUrls()
  {
    $criteria = new CDbCriteria(array('select' => 'base, target'));
    $criteria->compare('type_id', RedirectType::TYPE_REPLACE);
    $criteria->compare('visible', 1);

    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(Redirect::model()->tableName(), $criteria);

    foreach($command->queryAll() as $row)
    {
      if( RedirectHelper::isRegExp($row['base']) )
      {
        $this->replacedPatterns[$row['base']] = $row['target'];
      }
      else
      {
        $this->replacedUrls[$row['base']] = $row['target'];
      }
    }
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
