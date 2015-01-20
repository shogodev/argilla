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

  protected $importCommentsUrl = 'http://import.cackle.me/api/import-wordpress-comments';

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

  /**
   * @param string $data данные в формате xml
   * @param bool $last последняя запись
   */
  public function sendComment($data, $last)
  {
    return $this->sendData($this->importCommentsUrl, $data, $last);
  }

  private function sendData($url, $dataXml, $last)
  {
    $params = array(
      'siteId' => $this->siteId,
      'siteApiKey' => $this->siteApiKey,
      'accountApiKey' => $this->accountApiKey,
      'wxr' => $dataXml,
      'eof' => $last
    );

    if( !extension_loaded('curl') )
      throw new Exception('Curl module are not installed!');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-type" => "application/x-www-form-urlencoded; charset='utf-8'",
      "Accept" =>	"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    ));
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    $query = http_build_query($params, '', '&');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($ch);
    $error  = curl_error($ch);
    curl_close($ch);

    if( $error )
      throw new CHttpException(500, $error);

    return $result;
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