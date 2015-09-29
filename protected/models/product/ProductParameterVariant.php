<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property integer $id
 * @property string $param_id
 * @property string $name
 * @property string $position
 * @property string $url
 * @property string $notice
 *
 * @property ProductParameterName $parameterName
 *
 * @method static ProductParameterVariant model(string $class = __CLASS__)
 */
class ProductParameterVariant extends FActiveRecord
{
  const BASE_UPLOAD_PATH = 'f/upload/images/color/';

  public function relations()
  {
    return array(
      'parameterName' => array(self::BELONGS_TO, 'ProductParameterName', 'param_id')
    );
  }

  public function tableName()
  {
    return '{{product_param_variant}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => "IF({$alias}.position=0, 1, 0), {$alias}.position ASC, {$alias}.name"
    );
  }

  public function __toString()
  {
    return $this->name;
  }

  public function getImage($imageAttribute = 'notice')
  {
    return !empty($this->{$imageAttribute}) ? Yii::app()->request->hostInfo.'/'.self::BASE_UPLOAD_PATH.$this->{$imageAttribute} : '/i/sp.gif';
  }

  /**
   * @param ProductParameterName[] $parameterNames
   */
  public function setVariants(array $parameterNames)
  {
    $variantIds = array();
    foreach($parameterNames as $name)
      $variantIds = CMap::mergeArray($variantIds, $name->getVariantKeys());

    $variants = $this->findAllByAttributes(array('id' => $variantIds), new CDbCriteria(array('index' => 'id')));

    foreach($parameterNames as $parameterName)
    {
      $parameterName->setVariants($variants);
    }
  }
}