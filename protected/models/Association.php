<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @property string $src
 * @property string $src_frontend
 * @property int $src_id
 * @property string $dst
 * @property string $dst_frontend
 * @property int $dst_id
 *
 * @method static Association model(string $class = __CLASS__)
 */
class Association extends FActiveRecord
{
  /**
   * @param FActiveRecord $source
   *
   * @return $this
   */
  public function setSource(FActiveRecord $source)
  {
    $this->getDbCriteria()->compare('src_frontend', get_class($source));
    $this->getDbCriteria()->compare('src_id', $source->getPrimaryKey());
    return $this;
  }

  /**
   * @param FActiveRecord $dst
   *
   * @return $this
   */
  public function setDestination(FActiveRecord $dst)
  {
    $this->getDbCriteria()->compare('dst_frontend', get_class($dst));
    return $this;
  }

  /**
   * @return FActiveRecord[]
   */
  public function getAll()
  {
    $data = array();

    /**@var Association $entry*/
    foreach( $this->findAll() as $entry )
    {
      $class = $entry->dst_frontend;
      $model = $class::model()->findByPk($entry->dst_id);

      if( !empty($model) )
        $data[] = $model;
    }

    return $data;
  }
}