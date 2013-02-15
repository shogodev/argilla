<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 26.10.12
 */

class FMultiFileUpload extends CMultiFileUpload
{
  public $form;

  public function init()
  {
    $modelName = get_class($this->model).rand(0, 1000);

    if( !isset($this->accept) )
      $this->accept = implode('|', $this->model->fileTypes);

    if( !isset($this->duplicate) )
      $this->duplicate = 'Данный файл уже добавлен!';

    if( !isset($this->denied) )
      $this->denied = 'Вы не можете добавлять файлы данного типа';

    $this->options = CMap::mergeArray(array(
                                        'max'  => $this->model->maxFiles,
                                        'list' => '#'.$modelName.'_file_wrap_list'
                                      ), $this->options);

    if( !isset($this->htmlOptions['size']) )
      $this->htmlOptions['size'] = 1;
  }
}