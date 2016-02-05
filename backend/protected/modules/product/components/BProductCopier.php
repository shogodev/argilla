<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.components
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
   * @param $withImages
   *
   * @return integer copied product id
   */
  public function copy($withImages = false)
  {
    $this->copy = $this->copyModel($this->origin, null, array('parent' => $this->origin->id));

    $this->copyRelations($this->copy, $this->origin, 'assignment');
    $this->copyRelations($this->copy, $this->origin, 'associations');

    $this->copyParams();

    if( $withImages )
    {
      $this->copyImages($this->copy, $this->origin);
    }

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

  protected function copyImages(BActiveRecord $copyModel, BActiveRecord $originModel)
  {
    $images = BProductImg::model()->findAllByAttributes(array('parent' => $originModel->id));
    $imageThumbsPrefixList = $this->getThumbsPrefixList();

    $path = realpath(Yii::getPathOfAlias('frontend').'/../f/product').'/';
    foreach($images as $image)
    {
      if( $fileName = $this->copyFiles($path, $image->name, $imageThumbsPrefixList) )
      {
        $newImage = new BProductImg('copy');
        $newImage->attributes = $image->attributes;
        $newImage->setAttribute('parent', $copyModel->primaryKey);
        $newImage->setAttribute('name', $fileName);

        $newImage->save();
      }
    }
  }

  protected function copyFiles($path, $fileName, $imageThumbsPrefixList)
  {
    if( !file_exists($path.$fileName) )
      return null;

    while( 1 )
    {
      $newFileName = UploadHelper::doUniqueFilename($fileName);
      if( !file_exists($path.$newFileName) )
      {
        foreach($imageThumbsPrefixList as $prefix)
        {
          if( file_exists($path.$prefix.$fileName) )
          {
            copy($path.$prefix.$fileName, $path.$prefix.$newFileName);
            chmod($path.$prefix.$newFileName, 0775);
          }
        }

        return $newFileName;
      }
    }
  }

  protected function getThumbsPrefixList()
  {
    $prefixList = array();
    $thumbsSettings = Arr::get(Yii::app()->getModule('product')->getThumbsSettings(), 'product');

    foreach($thumbsSettings as $key => $value)
    {
      $prefixList[] = $key == 'origin' ? '' : $key.'_';
    }

    return $prefixList;
  }
}