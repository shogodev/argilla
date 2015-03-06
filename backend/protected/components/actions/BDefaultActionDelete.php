<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.actions
*/
class BDefaultActionDelete extends CAction
{
  public $redirectTo = 'index';

  /**
   * @var BActiveRecord
   */
  public $model;

  public function __construct($controller, $id)
  {
    parent::__construct($controller, $id);

    $this->model = Yii::app()->controller->loadModel(Yii::app()->request->getQuery('id'));
  }

  public function run()
  {
    if( Yii::app()->request->isPostRequest )
    {
      if( $this->model->asa('nestedSetBehavior') )
        $result = $this->model->deleteNode();
      else
        $result = $this->model->delete();

      if( $result )
      {
        $this->deleteAssociations();

        Yii::app()->user->setFlash('success', 'Запись успешно удалена.');

        if( !Yii::app()->request->isAjaxRequest )
        {
          $returnUrl = Yii::app()->request->getParam('returnUrl');
          $this->controller->redirect(isset($returnUrl) ? $returnUrl : array($this->redirectTo));
        }
      }
      else
        throw new CHttpException(400, 'Не могу удалить запись.');
    }
    else
      throw new CHttpException(400, 'Некорректный запрос.');
  }

  protected function deleteAssociations()
  {
    BAssociation::model()->deleteAssociations($this->model);
  }
}