<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.help.controllers
 */
class BHelpController extends BController
{
  public function actionIndex()
  {
    $this->render('index');
  }

  public function loadModel($id, $modelClass = null)
  {
    return null;
  }
}