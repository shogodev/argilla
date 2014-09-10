<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class CackleApi
{
  protected $commentsUrl = 'http://cackle.me/api/2.0/comment/list.json';

  protected $reviewsUrl = 'http://cackle.me/api/2.0/review/list.json';

  protected $siteId;

  protected $accountApiKey;

  protected $siteApiKey;

  public function __construct()
  {
    $configPath = Yii::getPathOfAlias('frontend.config.cackle').'.php';
    if( file_exists($configPath) )
    {
      $config = require($configPath);
      $this->siteId = $config['siteId'];
      $this->accountApiKey = $config['accountApiKey'];
      $this->siteApiKey = $config['siteApiKey'];
    }
    else
    {
      throw new CHttpException('500', 'Не найден кофигурационный файл cackle.php в папке config');
    }
  }

  /**
   * @param null $modified
   * @param null $page
   * @param null $size
   *
   * @return CackleResponseReviews
   */
  public function getReviews($page = null, $size = null, $modified = null)
  {
    $data = $this->getData($this->reviewsUrl, $this->getRequestParams($modified, $page, $size));

    return $data ? $data->reviews : null;
  }

  /**
   * @param null $page
   * @param null $size
   * @param null $modified
   *
   * @return CackleResponseComments
   */
  public function getComments($page = null, $size = null, $modified = null)
  {
    $data = $this->getData($this->commentsUrl, $this->getRequestParams($modified, $page, $size));

    return $data ? $data->comments : null;
  }

  private function getData($url, $parameters = array())
  {
    $curl = new Curl();
    $result = $curl->get($url, $parameters);
    $error = $curl->getLastError();

    if( $error )
      throw new CHttpException(500, $error);

    return json_decode($result);
  }

  private function getRequestParams($modified = null, $page = null, $size = null)
  {
    $params = array(
      'id' => $this->siteId,
      'siteApiKey' => $this->siteApiKey,
      'accountApiKey' => $this->accountApiKey
    );

    if( $modified ) $params['modified'] = $modified;
    if( $page ) $params['page'] = $page;
    if( $size ) $params['size'] = $size;

    return $params;
  }
}