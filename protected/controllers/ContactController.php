<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class ContactController extends FController
{
  public function actionIndex()
  {
    $model = Contact::model()->findByAttributes(array('sysname' => 'contacts'));

    $this->breadcrumbs = array('Контакты');

    $this->render('index', array('model' => $model));
  }
}