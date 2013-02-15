<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.widgets.form.BInput
 */
Yii::import('bootstrap.widgets.input.TbInput');

abstract class BInput extends TbInput
{
  public function run()
  {
    switch ($this->type)
    {
      case 'text':
        $this->text();
        break;

      case 'ckeditor':
        $this->ckeditor();
        break;

      case 'datePicker':
        $this->datePicker();
        break;

      case 'files':
        $this->files();
        break;

      case 'directory':
        $this->directory();
        break;

      case 'dependedInput':
        $this->dependedInput();
        break;

      case 'autocomplete':
        $this->autocomplete();
        break;

      case 'related':
        $this->related();
        break;

      case 'coordinates':
        $this->coordinates();
        break;

      case 'upload':
        $behavior = $this->model->behaviors();
        if( empty($behavior['uploadBehavior']) )
          throw new CException($this->type.': Failed to run widget! Model must realise `uploadBehavior` behavior.');
        $this->upload();
        break;

      default:
        parent::run();
    }
  }
}