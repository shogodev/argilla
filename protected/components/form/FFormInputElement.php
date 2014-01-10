<?php
class FFormInputElement extends CFormInputElement
{
  public $baseType;

  protected $defaultTemplate = "<div class=\"text-container\">\n{label}\n<div class=\"pdb\"><span class=\"inp_container\">{input}</span>\n{hint}\n{error}</div>\n</div>\n";

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
      '{hint}'  => $this->renderHint(),
      '{error}' => $this->getParent()->showErrorSummary ? '' : $this->renderError(),
    );

    return strtr($this->getLayout(), $output);
  }

  public function getLabel()
  {
    if($this->_label!==null)
      return $this->_label;
    else
      return $this->getParent()->getModel()->getAttributeLabel(preg_replace('/(\[\w+\])?(\w+)/', '$2', $this->name));
  }
  
    public function setLabel($value)
  {
    $this->_label=$value;
  }

  /**
   * Alias of getTemplate()
   *
   * @return string
   */
  protected function getLayout()
  {
    return $this->getTemplate();
  }

  /**
   * Получение шаблона отображения для каждого из типов полей
   * Для более общего отображения разных классов одинаковых типов полей
   * используется свойство baseType
   *
   * Если оно не установлено и не существует метод получения шаблона для текущего типа поля,
   * то возвращает defaultLayout, общий для всех полей
   *
   * @return string
   */
  protected function getTemplate()
  {
    if( !empty($this->baseType) )
      $typeName = ucfirst($this->baseType);
    else
      $typeName = ucfirst($this->type);

    $methodName = 'get' . $typeName . 'Template';

    if( method_exists($this, $methodName) )
      return $this->$methodName();
    else
      return $this->getDefaultTemplate();
  }

  /**
   * Получение общего для всех полей шаблона отображения
   *
   * @return string
   */
  protected function getDefaultTemplate()
  {
    return $this->defaultTemplate;
  }

  protected function getFileTemplate()
  {
    return "<div class=\"text-container\">
              {label}
              <div class=\"pdb\">
                <div class=\"fileinput-button btn btn-red\">
                  Выбрать файл
                  {input}
                </div>
                <div id=\"" . get_class($this->getParent()->getModel()) . '_' . $this->name . "_file_wrap_list\" class=\"MultiFile-list\"></div>
                {hint}{error}
              </div>
            </div>";
  }
}