<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class RetailCrmProduct
 * Классы создана для отключения сортировки для выгрузки в retailcrm, чтобы mysql отдовал данные без глюков (https://redmine.shogo.ru/issues/28205#note-15)
 */
class RetailCrmProduct extends Product
{
  public function tableName()
  {
    return '{{product}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }
}