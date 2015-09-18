<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.extensions.upload
 */
class UploadForm extends CFormModel
{
  /**
   * @var CUploadedFile
   */
  public $file;

  public $mime_type;

  public $size;

  public $name;

  public function rules()
  {
    return array(
      array('file', 'file'),
      array('file', 'file', 'types' => 'jpg, png, gif, jpeg, doc, docx, pdf, rar, xls, zip, csv', 'on' => 'create'),
    );
  }
}
