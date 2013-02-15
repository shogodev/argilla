<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.actions
 */
Yii::import('backend.modules.product.models.*');
Yii::import('backend.modules.info.models.*');

class BSaveAssociationAction extends CAction
{
  public function run($src, $srcId, $dst)
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $ids   = Yii::app()->request->getPost('ids');
      $value = Yii::app()->request->getPost('value');

      $src   = preg_replace("/".strtolower(BApplication::CLASS_PREFIX)."([a-z]+)/", "$1", $src);
      $class = BApplication::CLASS_PREFIX.ucfirst($src);
      $class = new $class;
      $model = $class->findByPk($srcId);

      if( $model )
      {
        if( !$value )
        {
          BAssociation::model()->deleteAssociations($model, $dst, $ids);
          return;
        }

        BAssociation::model()->updateAssociations($model, $dst, !is_array($ids) ? array($ids) : $ids, !$value);
      }
    }
    else
      throw new CHttpException(500, 'Некорректный запрос.');
  }
}