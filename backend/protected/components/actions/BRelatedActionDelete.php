<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.actions
*/
class BRelatedActionDelete extends CAction
{
  public function run()
  {
    if( Yii::app()->request->isAjaxRequest )
    {
      $id           = Yii::app()->request->getPost('id');
      $relation     = Yii::app()->request->getPost('relation');
      $model        = new $this->controller->modelClass;
      $className    = $model->getActiveRelation($relation)->className;
      $relatedModel = $className::model()->findByPk($id);
      $result       = $relatedModel->delete();

      if( !$result )
        throw new CHttpException(500, 'Не могу удалить запись.');
    }
    else
      throw new CHttpException(500, 'Некорректный запрос.');
  }
}