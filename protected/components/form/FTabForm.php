<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.form
 */
class FTabForm extends FForm
{
  public $tabularKey;

  /**
   * @param mixed $config
   * @param null $model
   * @param FForm $parent
   * @param int $tabularKey
   */
  public function __construct($config, $model = null, FForm $parent = null, $tabularKey = 0)
  {
    $this->tabularKey       = $tabularKey;
    $this->loadFromSession  = $parent ? $parent->loadFromSession : false;
    $this->clearAfterSubmit = $parent ? $parent->clearAfterSubmit : false;

    parent::__construct($config, $model, $parent);
  }

  public function setElements($elements)
  {
    $collection = $this->getElements();
    foreach($elements as $name => $config)
      $collection->add(preg_match("/\[\w+\]/", $name) ? $name : "[$this->tabularKey]".$name, $config);
  }

  public function saveToSession()
  {
    // todo: сделать проверку на password и не сохранять его
    if( Yii::app()->request->isPostRequest && $this->getModel() !== null )
    {
      $class = get_class($this->getModel());

      if( !isset($_SESSION['form_'.$class][$this->tabularKey]) )
        $_SESSION['form_'.$class][$this->tabularKey] = array();

      $postParams    = $_POST[$class][$this->tabularKey];
      $sessionParams = CMap::mergeArray($_SESSION['form_'.$class][$this->tabularKey], $postParams);

      $_SESSION['form_'.$class][$this->tabularKey] = $sessionParams;
    }

    foreach($this->getElements() as $element)
      if( $element instanceof self )
        $element->saveToSession();
  }

  public function loadFromSession()
  {
    $this->restore($_SESSION, 'form_');
  }

  public function loadData()
  {
    $this->restore($_POST);
  }

  public function isTabular()
  {
    return isset($this->tabularKey);
  }

  /**
   * @param $srcData
   * @param string $dataPrefix
   */
  public function restore($srcData, $dataPrefix = '')
  {
    if( $this->getModel() !== null )
    {
      $class = get_class($this->getModel());

      if( !isset($srcData[$dataPrefix.$class][$this->tabularKey]) )
        $srcData[$dataPrefix.$class][$this->tabularKey] = array();

      $this->getModel()->setAttributes($srcData[$dataPrefix.$class][$this->tabularKey]);
    }

    foreach($this->getElements() as $element)
      if( $element instanceof self )
        $element->restore($srcData);
  }

  protected function performAjaxValidation()
  {
    $result = array();

    if( $this->getModel() !== null )
    {
      $this->getModel()->validate();

      foreach($this->getModel()->getErrors() as $attribute => $errors)
        $result[CHtml::activeId($this->getModel(),'['.$this->tabularKey.']'.$attribute)] = $errors;

      foreach($this->getElements() as $element)
        if($element instanceof self)
          $result = CMap::mergeArray($result, $element->performAjaxValidation());
    }

    return $result;
  }
}