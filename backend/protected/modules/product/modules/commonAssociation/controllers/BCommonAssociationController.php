<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.controllers
 */
class BCommonAssociationController extends BController
{
  public $showInMenu = false;

  public $modelClass = 'BCommonAssociation';

  public $name = 'Общие привязки';

  public function actionAssociation($src, $srcId, $dst)
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $id = Yii::app()->request->getPost('ids');
      $value = Yii::app()->request->getPost('value');

      if( BProduct::model()->findByPk($srcId) )
      {
        BCommonAssociation::model()->makeAssociation($srcId, $id, $value, $dst);
      }
    }
    else
    {
      throw new CHttpException(500, 'Некорректный запрос.');
    }
  }
}