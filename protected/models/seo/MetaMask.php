<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.seo
 */
class MetaMask extends FActiveRecord
{
  public function tableName()
  {
    return '{{seo_meta_mask}}';
  }

  public function getData($url)
  {
    if( strlen($url) > 1 && substr($url, -1) == '/' )
      $url = substr($url, 0, strlen($url) - 1);

    $data = $this->getDataByUrl($url);
    if( empty($data) )
      $data = $this->findDataByMask($url);

    return $data;
  }

  private function getDataByUrl($url)
  {
    return $this->find('url_mask=:url AND visible=:visible', array(':url' => $url, ':visible' => '1'));
  }

  private function findDataByMask($url)
  {
    $data = $this->findAll('visible=:visible', array(':visible' => 1));

    $masks = array();
    foreach($data as $value)
      $masks[] = $value->url_mask;

    //todo: Обработка маски откладывается до лучших времен

    return array();
  }
}
?>