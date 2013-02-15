<?php
class UploadForm extends CFormModel
{
  public $file;

  public $mime_type;

  public $size;

  public $name;

  /**
   * Declares the validation rules.
   * The rules state that username and password are required,
   * and password needs to be authenticated.
   */
  public function rules()
  {
    return array(
      array('file', 'file'),
      //array('file', 'file', 'types' => 'jpg, png, gif, jpeg', 'on'=>'create'),
      //array('file', 'file', 'allowEmpty' => true, 'types' => 'jpg, png, gif, jpeg', 'on' => 'update'),
    );
  }
}
