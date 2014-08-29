<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
abstract class FRedirectComponent extends CApplicationComponent
{
  /**
   * @var CDbCriteria
   */
  protected $criteria;

  /**
   * @var array
   */
  protected $redirectUrls = array();

  /**
   * @var array
   */
  protected $redirectPatterns = array();

  public function init()
  {
    parent::init();
    $this->initRedirects();
  }

  /**
   * @return array
   */
  protected function getRedirectUrls()
  {
    return $this->redirectUrls;
  }

  protected function getRedirectPatterns()
  {
    return $this->redirectPatterns;
  }

  /**
   * @param string $url
   *
   * @return array|null
   */
  protected function findByKey($url)
  {
    return Arr::get($this->getRedirectUrls(), $url);
  }

  /**
   * @param string $url
   *
   * @return array|null
   */
  protected function findByPattern($url)
  {
    foreach($this->getRedirectPatterns() as $pattern => $data)
    {
      if( @preg_match($pattern, $url) )
      {
        $data['target'] = preg_replace($pattern, $this->prepareReplacement($data['target']), $url);
        return $data;
      }
    }

    return null;
  }

  protected function prepareReplacement($string)
  {
    return preg_replace_callback("/\([^)]+\)/", function($matches) {
      static $position = 0;
      return '$'.++$position;
    }, trim($string, '#'));
  }

  protected function initRedirects()
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(Redirect::model()->tableName(), $this->criteria);

    foreach($command->queryAll() as $row)
    {
      $this->addRedirect($row['id'], $row['base'], $row['target'], $row['type_id']);
    }
  }

  abstract protected function addRedirect($id, $base, $target, $type);
}