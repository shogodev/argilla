<?php

class MetaMask extends FActiveRecord
{
  public function tableName()
  {
    return '{{meta_mask}}';
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
    //Dumper::pre($masks);

    return array();
  }
}
?>