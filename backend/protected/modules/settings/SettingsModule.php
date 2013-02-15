<?php

class SettingsModule extends BModule
{
  public $defaultController = 'settings';

  public $name = 'Настройки';

  public $group = 'settings';

  public function init()
  {
    $this->setImport(array(
      'settings.models.*',
      'settings.components.*',
      'settings.controllers.*',
    ));
  }
}
