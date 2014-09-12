<?php
class ValidatorTestModel extends CFormModel
{
  public $login;

  public function rules()
  {
    return array(
      array('login', 'LoginValidator'),
    );
  }
}
