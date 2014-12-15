<?php
class DealerModule extends BModule
{
  public $enabled = false;

  public $defaultController = 'BDealer';

  public $name = 'Дилеры';

  /**
   * @var bool $userDependency - Привязать к пользователям
   */
  public $userDependency = true;

  protected function getExtraDirectoriesToImport()
  {
    return array(
      'backend.modules.user.models.BFrontendUser',
      'backend.modules.dealer.FilialWidget'
    );
  }
}
