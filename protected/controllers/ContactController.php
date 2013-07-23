<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers
 */
class ContactController extends FController
{
  public function actionIndex()
  {
    $model = Contact::model()->findAll();

    $this->breadcrumbs = array('Контакты');

    $this->render('/contact', array('model' => $model));
  }
}