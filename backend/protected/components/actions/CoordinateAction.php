<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class CoordinateAction extends CAction
{
  public $modelName;

  public $addressAttribute;

  public function init()
  {
    if( is_null($this->modelName) )
      throw new CHttpException(500, 'Укажите параметор modelName для поведения '.__CLASS__);

    if( is_null($this->addressAttribute) )
      throw new CHttpException(500, 'Укажите параметор addressAttribute для поведения '.__CLASS__);
  }

  public function run()
  {
    $this->init();

    $id = Yii::app()->request->getParam('id');
    $attribute = Yii::app()->request->getParam('attribute');
    /**
     * @var BActiveRecord $model
     */
    $model = new $this->modelName;
    $model = $model->findByPk($id);

    $this->controller->render('//coordinate', array(
      'coordinates' => $model->{$attribute},
      'address' => $model->{$this->addressAttribute},
      'attribute' => CHtml::getIdByName(CHtml::activeName($model, $attribute)),
    ));
  }
} 