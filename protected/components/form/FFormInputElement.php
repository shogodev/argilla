<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>, Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.form
 */
class FFormInputElement extends CFormInputElement
{
  public static $coreTypes = array(
    'text' => 'activeTextField',
    'hidden' => 'activeHiddenField',
    'password' => 'activePasswordField',
    'textarea' => 'activeTextArea',
    'file' => 'activeFileField',
    'radio' => 'activeRadioButton',
    'checkbox' => 'activeCheckBox',
    'listbox' => 'activeListBox',
    'dropdownlist' => 'activeDropDownList',
    'checkboxlist' => 'activeCheckBoxList',
    'radiolist' => 'activeRadioButtonList',
    'url' => 'activeUrlField',
    'email' => 'activeEmailField',
    'number' => 'activeNumberField',
    'range' => 'activeRangeField',
    'date' => 'activeDateField',
    'tel' => 'activeTelField'
  );

  public $layout = null;

  protected $defaultLayout = "<div class=\"text-container\">\n{label}\n<div class=\"pdb\"><span class=\"input-wrap\">{input}</span>\n{hint}\n{error}</div>\n</div>\n";

  private $_label;

  /**
   * @return string
   */
  public function render()
  {
    if( $this->type === 'hidden' )
      return $this->renderInput();

    $output = array(
      '{label}' => $this->renderLabel(),
      '{input}' => $this->renderInput(),
      '{hint}' => $this->renderHint(),
      '{error}' => $this->getParent()->showErrorSummary ? '' : $this->renderError(),
    );

    return strtr($this->getLayout(), $output);
  }

  public function getLabel()
  {
    if( $this->_label !== null )
      return $this->_label;
    else
      return $this->getParent()->getModel()->getAttributeLabel(preg_replace('/(\[\w+\])?(\w+)/', '$2', $this->name));
  }

  /**
   * @param string $value
   */
  public function setLabel($value)
  {
    $this->_label = $value;
  }

  public function renderInput()
  {
    if( $this->getParent()->getModel()->asa('relatedFormElements') )
      $this->getParent()->getModel()->registerScriptByElement($this);

    if( isset(self::$coreTypes[$this->type]) )
    {
      $method = self::$coreTypes[$this->type];
      if( strpos($method, 'List') !== false )
        return CHtml::$method($this->getParent()->getModel(), $this->name, $this->items, $this->attributes);
      else
        return CHtml::$method($this->getParent()->getModel(), $this->name, $this->attributes);
    }
    else
    {
      $attributes = $this->attributes;
      $attributes['model'] = $this->getParent()->getModel();
      $attributes['attribute'] = $this->name;
      ob_start();
      $this->getParent()->getOwner()->widget($this->type, $attributes);

      return ob_get_clean();
    }
  }

  public function getLayout()
  {
    return $this->layout ? $this->layout : $this->getElementsLayout($this->parent);
  }

  /**
   * @param FForm $form
   *
   * @return string
   */
  protected function getElementsLayout($form)
  {
    if( is_null($form->elementsLayout) )
    {
      if( $form->parent instanceof CForm )
        return $this->getElementsLayout($form->parent);
      else
        return $this->defaultLayout;
    }

    return $form->elementsLayout;
  }
}