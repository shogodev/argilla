<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
class FRedirectComponent extends CApplicationComponent
{
  /**
   * @var CDbCriteria
   */
  protected $criteria;

  /**
   * @var array
   */
  private $redirectUrls = array();

  /**
   * @var array
   */
  private $redirectPatterns = array();

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
        $data['target'] = preg_replace($pattern, $data['target'], $url);
        return $data;
      }
    }

    return null;
  }

  protected function initRedirects()
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand(Redirect::model()->tableName(), $this->criteria);

    foreach($command->queryAll() as $row)
    {
      $data = array(
        'target' => $row['target'],
        'type_id' => $row['type_id']
      );

      if( RedirectHelper::isRegExp($row['base']) )
      {
        $this->redirectPatterns[$row['base']] = $data;
      }
      else
      {
        $this->redirectUrls[$row['base']] = $data;
      }
    }
  }
}