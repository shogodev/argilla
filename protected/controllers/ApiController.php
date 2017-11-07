<?php

class ApiController extends FController
{
  public function actionSettings($param)
  {
    $setting = Settings::model()->findByAttributes(['param' => $param]);
    echo $setting->value;
  }
}
