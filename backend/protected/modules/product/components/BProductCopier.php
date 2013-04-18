<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BProductCopier extends BAbstractModelCopier
{
  /**
   * @var BProduct
   */
  protected $copy;

  /**
   * @var BProduct
   */
  protected $origin;

  /**
   * @param $id
   * @throws CHttpException
   */
  public function __construct($id)
  {
    $this->origin = BProduct::model()->findByPk($id);

    if( !$this->origin )
      throw new CHttpException(500, 'Объект не найден');
  }

  /**
   * @return integer copied product id
   */
  public function copy()
  {
    $this->copy = $this->copyModel($this->origin, null, array('parent' => $this->origin->id));

    $this->copyRelations($this->copy, $this->origin, 'assignment');
    $this->copyRelations($this->copy, $this->origin, 'associations');
    $this->copyRelations($this->copy, $this->origin, 'videos');

    $this->copyParams();

    return $this->copy->id;
  }

  protected function copyParams()
  {
    $this->origin->getMetaData()->addRelation('params', array(
      BActiveRecord::HAS_MANY,
      'BProductParam',
      'product_id',
    ));

    $this->copyRelations($this->copy, $this->origin, 'params');
  }
}