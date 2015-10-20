<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 * Пример исполбзования в форме
 * 'img' => array(
 *   'type' => 'FMultiFileUpload',
 *   'form' => $this,
 *  ),
 */
/**
 * Class FMultiFileUpload
 * @var FActiveFileRecord $model
 */
class FMultiFileUpload extends CMultiFileUpload
{
  /**
   * @var FForm
   */
  public $form;

  public function init()
  {
    if( !($this->model instanceof FActiveFileRecord) )
      throw new CHttpException(500, 'Модель '.get_class($this->model).' должна быть потомком FActiveFileRecord');

    if( !($this->form instanceof FForm) )
      throw new CHttpException(500, 'Атрибут form должен ссылатся на родительскую форму');

    if( $this->form->ajaxSubmit )
      throw new CHttpException(500, 'Необходимо выключить своство ajaxSubmit в форме '.get_class($this->form));

    if( !isset($this->accept) )
      $this->accept = implode('|', $this->model->fileTypes);

    if( !isset($this->duplicate) )
      $this->duplicate = 'Данный файл уже добавлен!';

    if( !isset($this->denied) )
      $this->denied = 'Вы не можете добавлять файлы данного типа';

    $this->options = CMap::mergeArray(array('max' => $this->model->maxFiles), $this->options);

    if( !isset($this->htmlOptions['size']) )
      $this->htmlOptions['size'] = 1;
  }


}