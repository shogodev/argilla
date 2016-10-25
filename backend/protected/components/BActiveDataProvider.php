<?php
/**
 * Данный класс используется для инициализации выбора количества элементов на страницу
 * Инициализируется вместо CActiveDataProvider
 *
 * Для настройки свойств класса используется BActiveDataProvider::setPageSizeElements($elements)
 * @example
 * <code>
 *  // инициализация датапровайдера
 *  $dataProvider = new BActiveDataProvider('BNews', array('sort' => array('defaultOrder' => 'date DESC')));
 *  $dataProvider->pagination->setPageSizeElements(10000000 => 'Все', 5 => 5, 10 => 10, 25 => 25);
 * </code>
 *
 * Для того чтобы получить форму необходимо вызвать BActiveDataProvider::getPagination()->getPageSizeForm()
 * @example
 * <code>
 *  $dataProvider->pagination->getPageSizeForm();
 * </code>
 * При этом произойдет отобращений как самой формы, так и скрипта, который перехватывает эвэнт onChange поля формы
 * и автоматически сабмитит.
 *
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 *
 *
 * @property BPagination $pagination
 */
Yii::import('frontend.share.components.SActiveDataProvider');
/**
 * Class BActiveDataProvider
 * @property BPagination $pagination
 */
class BActiveDataProvider extends SActiveDataProvider
{
  public function __construct($modelClass, $config = array())
  {
    parent::__construct($modelClass, $config);
    $this->setPagination([
      'class' => 'BPagination',
      'pageVar' => $this->getId().'_page',
      'pageSizeVar' => $this->getId().'_count',
    ]);
  }
}