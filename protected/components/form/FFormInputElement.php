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
  public $layout = null;

  protected $defaultLayout = '<div class="form-row m7">{label}<div class="form-field">{input}{hint}{error}</div></div>';

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