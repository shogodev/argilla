<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class DealerBehavior extends SBehavior
{
  const DEALER_TABLE = '{{dealer}}';

  const DEALER_FILIAL_TABLE = '{{dealer_filial}}';

  const DEALER_CITY_TABLE = '{{dealer_city}}';

  private $filialsData;

  public function getFilials()
  {
    $filials = array();

    foreach($this->findFilials() as $filial)
    {
      $filials[] = $this->createFilialByData($filial);
    }

    return $filials;
  }

  public function getFilialGroupByCity()
  {
    $cities = array();

    foreach($this->findFilials() as $filial)
    {
      if( !isset($cities[$filial['city_id']]) )
        $cities[$filial['city_id']] = array('name' => $filial['city'], 'filials' => array());

      $cities[$filial['city_id']]['filials'][] = $this->createFilialByData($filial);
    }

    return $cities;
  }

  private function findFilials()
  {
    $select = array(
      'f.id',
      'f.name',
      'site_url',
      'address',
      'f.phone',
      'f.phone_additional',
      'worktime',
      'c.name AS city',
      'd.img',
      'city_id',
      'coordinates',
      'c.position as city_position'
    );

    if( is_null($this->filialsData) )
    {
      $criteria = new CDbCriteria();
      $criteria->select = implode(', ', $select);
      $criteria->join .= " JOIN ".self::DEALER_CITY_TABLE." AS c ON f.city_id = c.id";
      $criteria->join .= " JOIN ".self::DEALER_TABLE." AS d ON f.dealer_id = d.id";
      $criteria->condition = ' f.coordinates != "" ';

      $criteria->compare('d.visible', 1);
      $criteria->compare('f.visible', 1);

      $criteria->order = 'IF(c.position = 0, 9999, c.position), c.name, IF(f.position = 0, 9999, f.position), f.name';

      $command = Yii::app()->db->schema->commandBuilder->createFindCommand(self::DEALER_FILIAL_TABLE, $criteria, 'f');
      $this->filialsData = $command->queryAll();
    }

    return $this->filialsData;
  }

  private function createFilialByData($filialData)
  {
    $filial = array(
      'dealer' => array(
        'name' => $filialData['name'],
        'img' => !empty($filialData['img']) ? 'f/dealer/'.$filialData['img'] : '',
      ),
      'city' => $filialData['city'],
      'city_id' => $filialData['city_id'],
      'address' => implode(', ', array($filialData['city'], $filialData['address'])),
      'phone' => $filialData['phone'],
      'worktime' => $filialData['worktime'],
      'coordinates' => explode(',', $filialData['coordinates'])
    );

    if( !empty($filialData['site_url']) )
    {
      $filial['anchor'] = str_replace('http://', '', $filialData['site_url']);
      $filial['href'] = $filialData['site_url'];
    }

    if( !empty($filialData['phone']) || !empty($filialData['phone_additional']) )
    {
      $phones = array();

      if( !empty($filialData['phone']) )
        $phones[] = $filialData['phone'];

      if( !empty($filialData['phone_additional']) )
        $phones[] = $filialData['phone_additional'];
      $filial['phones'] = implode(',', $phones);
    }

    return $filial;
  }
}
