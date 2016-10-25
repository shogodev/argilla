<?php
/**
 * @author Dmitriy Ostashev <ostashev@shogo.ru>
 * @property string $actionUrl
 */
class BPagination extends CPagination
{
  const MAX_PAGE_SIZE = PHP_INT_MAX;

  /**
   * Массив элементов, используемых для вывода
   * в форме вариантов отображения элементов страниц
   * @var array
   */
  public $pageSizeList = [10 => 10, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, self::MAX_PAGE_SIZE => 'Все'];

  /** @var  int Идентефикатор переменной кол-ва элементов на странице */
  public $pageSizeVar;

  /** @var  string Url сабмита формы - выбора кол-ва элементов на странице */
  protected $_actionUrl;

  protected $_pageSize = self::DEFAULT_PAGE_SIZE;

  protected $_itemCount = 0;

  public function getPageSize()
  {
    if( !empty($_GET[$this->pageSizeVar]) )
      $this->_pageSize = $_GET[$this->pageSizeVar];

    return (int)$this->_pageSize === 0 ? self::DEFAULT_PAGE_SIZE : $this->_pageSize;
  }

  /**
   * @param $val
   */
  public function setActionUrl($val)
  {
    $this->_actionUrl = $val;
  }

  /**
   * @return string
   */
  public function getActionUrl()
  {
    if( $this->_actionUrl === null )
    {
      $route = Yii::app()->controller instanceof CController ? Yii::app()->controller->route : '';
      $params = $_GET;
      unset($params[$this->pageSizeVar], $params['ajax']);
      $this->_actionUrl = Yii::app()->createUrl($route, $params);
    }

    return $this->_actionUrl;
  }

  /**
   * @return integer number of pages
   */
  public function getPageCount()
  {
    return (int)(($this->_itemCount + $this->_pageSize - 1) / $this->_pageSize);
  }

  /**
   * @param integer $value total number of items.
   */
  public function setItemCount($value)
  {
    if( ($this->_itemCount = $value) < 0 )
      $this->_itemCount = 0;
  }

  /**
   * Создание формы для отображения вариантов количества элементов
   * @return string of form
   */
  public function getPageSizeForm()
  {
    $formProperties = ['class' => 'page-size'];

    $form = CHtml::beginForm($this->actionUrl, 'get', $formProperties);
    $form .= CHtml::tag('span', ['class' => 'page-size-title'], 'Отображать по: ');
    $form .= CHtml::dropDownList($this->pageSizeVar, $this->getPageSize(), $this->pageSizeList);
    $form .= CHtml::endForm();

    $formChangeHandler = <<<JS
$('body').on('change', '.{$formProperties['class']} select', function(){
  if ( window.History.enabled ) {
      var url = $(this).parents('form').attr('action').split('?');
      var params = url[1] === undefined ? [] : $.deparam.querystring('?'+url[1]);
      params[$(this).attr('id')] = $(this).val();
      window.History.pushState(null, document.title, decodeURIComponent($.param.querystring(url[0], params)));
  } else {
      $(this).parents('.{$formProperties['class']}').submit();
  }
});
JS;
    Yii::app()->getClientScript()->registerScript('pageSizeChangeHandler', $formChangeHandler, CClientScript::POS_READY);

    return $form;
  }
}