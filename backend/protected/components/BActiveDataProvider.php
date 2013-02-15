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
 *  $dataProvider->setPageSizeElements(10000000 => 'Все', 5 => 5, 10 => 10, 25 => 25);
 * </code>
 *
 * Для того чтобы получить форму необходимо вызвать BActiveDataProvider::getPageSizeForm()
 * @example
 * <code>
 *  $dataProvider->getPageSizeForm();
 * </code>
 * При этом произойдет отобращений как самой формы, так и скрипта, который перехватывает эвэнт onChange поля формы
 * и автоматически сабмитит.
 *
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
class BActiveDataProvider extends CActiveDataProvider
{
  const PAGINATION_PAGE_SIZE = 10;

  const MAX_PAGE_SIZE        = PHP_INT_MAX;

  /**
   * Хранит в себе строку тэгов формы с заданными параметрами
   *
   * @var string of html tags with form
   */
  protected $pageSizeForm;

  /**
   * Массив элементов, используемых для вывода
   * в форме вариантов отображения элементов страниц
   *
   * @var array
   */
  protected $pageSizeFormElements = array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 'all' => 'Все');

  /**
   * Имя переменной для передачи и перехвата в $_GET запросе
   *
   * @var staring
   */
  protected $attributeName;

  /**
   * Текущий размер вывода элементов на страницу
   *
   * @var int
   */
  protected $currentPageSize = self::PAGINATION_PAGE_SIZE;

  /**
   * @param mixed $modelClass
   * @param array $config
   */
  public function __construct($modelClass, array $config = array())
  {
    parent::__construct($modelClass, $config);

    $this->attributeName  = $this->getId() . '_count';
    $this->pagination     = array('pageSize' => $this->createPageSize());
  }

  /**
   * Возвращает текущее значение количества элементов на страницу
   *
   * @return int
   */
  public function getCurrentPageSize()
  {
    return $this->currentPageSize;
  }

  /**
   * Присваивание новых вариантов значений количества элементов
   *
   * @param array $elements
   */
  public function setPageSizeElements(array $elements)
  {
    $this->pageSizeFormElements = $elements;
  }

  /**
   * Получение текущего значения вывода количества элементов на страницу
   *
   * @return int
   */
  public function getPageSizeFormElements()
  {
    if( !empty($this->pageSizeFormElements['all']) )
    {
      unset($this->pageSizeFormElements['all']);
      $this->pageSizeFormElements[self::MAX_PAGE_SIZE] = 'Все';
    }

    return $this->pageSizeFormElements;
  }

  /**
   * Создание формы для отображения вариантов количества элементов
   *
   * @return string of form
   */
  public function getPageSizeForm()
  {
    if( empty($this->pageSizeForm) )
      $this->createPageSizeForm();

    return $this->pageSizeForm;
  }

  /**
   * Создание формы, включающей в себя <select>-поле с вариантом отображения количества элементов
   * Так же создания скрипта для обработки onChange эвэнтов изменения <select>
   */
  private function createPageSizeForm()
  {
    $formProperties = array('class' => 'page-size');

    $form  = CHtml::beginForm($this->getClearUrl(), 'get', $formProperties);
    $form .= CHtml::openTag('span', array('class' => 'page-size-title'));
    $form .= 'Отображать по: ';
    $form .= CHtml::closeTag('span');
    $form .= CHtml::dropDownList($this->attributeName, $this->currentPageSize, $this->getPageSizeFormElements());
    $form .= CHtml::endForm();

    $this->pageSizeForm = $form;

    $formChangeHandler = <<<EOD
  $('.{$formProperties['class']} select').live('change', function(){
    $(this).parents('.{$formProperties['class']}').submit();
  });
EOD;

    Yii::app()->getClientScript()->registerScript('pageSizeChangeHandler', $formChangeHandler, CClientScript::POS_READY);
  }

  /**
   * Создания количества элементов на страницу
   * Если установлен $_GET, содержащий $this->attributeName, то значение меняется со стандартного на новое
   *
   * @return int
   */
  private function createPageSize()
  {
    if( !empty($_GET[$this->getId() . '_count']) )
      $this->currentPageSize = $_GET[$this->attributeName];

    if( $this->currentPageSize === 'all' )
      $this->currentPageSize = self::MAX_PAGE_SIZE;

    return $this->currentPageSize;
  }

  /**
   * Получение чистого адреса для сабмита формы
   * Убирает текущий $_GET из запроса, сохраняя остальные параметры
   *
   * @return string
   */
  private function getClearUrl()
  {
    $params = $_GET;

    unset($params[$this->attributeName]);
    unset($params['ajax']);
    return Yii::app()->createUrl(Yii::app()->controller->uniqueId.'/'.Yii::app()->controller->getAction()->getId(), $params);
  }
}