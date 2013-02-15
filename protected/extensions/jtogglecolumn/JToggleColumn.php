<?php

/**
 * CToggleColumn class file.
 * @author Nikola Trifunovic <johonunu@gmail.com>
 * @link http://www.trifunovic.me/
 * @copyright Copyright &copy; 2012 Nikola Trifunovic
 * @license http://www.yiiframework.com/license/
 */
Yii::import('zii.widgets.grid.CGridColumn');

class JToggleColumn extends CGridColumn
{

  /**
   * @var string the attribute name of the data model. Used for column sorting, filtering and to render the corresponding
   * attribute value in each data cell. If {@link value} is specified it will be used to rendered the data cell instead of the attribute value.
   * @see value
   * @see sortable
   */
  public $name;

  /**
   * @var array the HTML options for the data cell tags.
   */
  public $htmlOptions = array('class' => 'toggle-column');

  /**
   * @var array the HTML options for the header cell tag.
   */
  public $headerHtmlOptions = array('class' => 'toggle-column');

  /**
   * @var array the HTML options for the footer cell tag.
   */
  public $footerHtmlOptions = array('class' => 'toggle-column');

  /**
   * @var string the label for the toggle button. Defaults to "toggle".
   * Note that the label will not be HTML-encoded when rendering.
   */
  public $checkedButtonLabel;

  /**
   * @var string the label for the toggle button. Defaults to "toggle".
   * Note that the label will not be HTML-encoded when rendering.
   */
  public $uncheckedButtonLabel;

  /**
   * @var string the image URL for the toggle button. If not set, an integrated image will be used.
   * You may set this property to be false to render a text link instead.
   */
  public $checkedButtonImageUrl;

  /**
   * @var string the image URL for the toggle button. If not set, an integrated image will be used.
   * You may set this property to be false to render a text link instead.
   */
  public $uncheckedButtonImageUrl;

  /**
   * @var array the configuration for toggle button.
   */
  public $toggle_button = array();

  /**
   * @var boolean whether the column is sortable. If so, the header cell will contain a link that may trigger the sorting.
   * Defaults to true. Note that if {@link name} is not set, or if {@link name} is not allowed by {@link CSort},
   * this property will be treated as false.
   * @see name
   */
  public $sortable = true;

  /**
   * @var mixed the HTML code representing a filter input (eg a text field, a dropdown list)
   * that is used for this data column. This property is effective only when
   * {@link CGridView::filter} is set.
   * If this property is not set, a text field will be generated as the filter input;
   * If this property is an array, a dropdown list will be generated that uses this property value as
   * the list options.
   * If you don't want a filter for this data column, set this value to false.
   * @since 1.1.1
   */
  public $filter;

  /**
   * @var string Name of the action
   */
  public $action;

  /**
   * @var string Assets url
   */
  private $_assetsUrl;

  /**
   * Returns assets url, where check and uncheck images are located
   * @return string
   */
  public function getAssetsUrl()
  {
    if( $this->_assetsUrl === null )
      $this->_assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/images');
    return $this->_assetsUrl;
  }

  /**
   * Initializes the column.
   * This method registers necessary client script for the button column.
   */
  public function init()
  {
    if( $this->name === null )
      $this->sortable = false;
    if( $this->name === null )
      throw new CException(Yii::t('toggle_column', 'Model attribute ("name") must be specified for CToggleColumn.'));

    $this->initDefaultButtons();

    $this->toggle_button['click'] = 'js:'.$this->toggle_button['click'];

    $this->registerClientScript();
  }

  /**
   * Initializes the default buttons (toggle).
   */
  protected function initDefaultButtons()
  {
    if( $this->checkedButtonLabel === null )
      $this->checkedButtonLabel = Yii::t('toggle_column', 'Выключить');
    if( $this->uncheckedButtonLabel === null )
      $this->uncheckedButtonLabel = Yii::t('toggle_column', 'Включить');
    if( $this->checkedButtonImageUrl === null )
      $this->checkedButtonImageUrl = $this->getAssetsUrl().'/toggle-on.png';
    if( $this->uncheckedButtonImageUrl === null )
      $this->uncheckedButtonImageUrl = $this->getAssetsUrl().'/toggle-off.png';

    if( $this->action === null )
      $this->action = 'toggle';

    if( !isset($this->toggle_button['htmlOptions']) )
      $this->toggle_button['htmlOptions'] = array('rel' => 'tooltip');

    $this->toggle_button = array(
      'url' => 'Yii::app()->controller->createUrl("'.$this->action.'",array("id"=>$data->primaryKey,"attribute"=>"'.$this->name.'"))',
      'options' => CMap::mergeArray(array('class' => $this->name.'_toggle'), $this->toggle_button['htmlOptions']),
    );

    if( Yii::app()->request->enableCsrfValidation )
    {
      $csrfTokenName = Yii::app()->request->csrfTokenName;
      $csrfToken     = Yii::app()->request->csrfToken;
      $csrf          = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
    }
    else
      $csrf = '';

    $this->toggle_button['click'] = <<<EOD
function() {
	var th=this;
	$.fn.yiiGridView.update('{$this->grid->id}', {
		type:'POST',
		url:$(this).attr('href'),$csrf
		success:function(data) {
			$.fn.yiiGridView.update('{$this->grid->id}');
		},
		ajaxUpdate:true
	});
	return false;
}
EOD;
  }

  /**
   * Registers the client scripts for the button column.
   */
  protected function registerClientScript()
  {
    $js = array();

    $function = CJavaScript::encode($this->toggle_button['click']);
    $class    = preg_replace('/\s+/', '.', $this->toggle_button['options']['class']);
    $js[]     = "jQuery('#{$this->grid->id} a.{$class}').live('click', $function);";

    if( $js !== array() )
      Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$this->id, implode("\n", $js));
  }

  /**
   * Renders the data cell content.
   * This method renders the view, update and toggle buttons in the data cell.
   *
   * @param integer $row the row number (zero-based)
   * @param mixed   $data the data associated with the row
   */
  protected function renderDataCellContent($row, $data)
  {
    ob_start();
    $this->renderButton($this->toggle_button, $row, $data);
    $toggle_button = ob_get_contents();
    ob_clean();
    ob_end_clean();
    echo $toggle_button;
  }

  /**
   * Renders the header cell content.
   * This method will render a link that can trigger the sorting if the column is sortable.
   */
  protected function renderHeaderCellContent()
  {
    if( $this->grid->enableSorting && $this->sortable && $this->name !== null )
      echo $this->grid->dataProvider->getSort()->link($this->name, $this->header);
    else if( $this->name !== null && $this->header === null )
    {
      if( $this->grid->dataProvider instanceof CActiveDataProvider )
        echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
      else
        echo CHtml::encode($this->name);
    }
    else
      parent::renderHeaderCellContent();
  }

  /**
   * Renders a toggle button.
   *
   * @param string  $id the ID of the button
   * @param array   $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
   * See {@link buttons} for more details.
   * @param integer $row the row number (zero-based)
   * @param mixed   $data the data object associated with the row
   */
  protected function renderButton($button, $row, $data)
  {
    if( $this->name !== null )
      $checked = CHtml::value($data, $this->name);

    $button['imageUrl'] = $checked ? $this->checkedButtonImageUrl : $this->uncheckedButtonImageUrl;
    $button['label']    = $checked ? $this->checkedButtonLabel : $this->uncheckedButtonLabel;

    $label   = $button['label'];
    $url     = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data' => $data, 'row' => $row)) : '#';
    $options = isset($button['options']) ? $button['options'] : array();
    if( !isset($options['title']) )
      $options['title'] = $label;
    if( isset($button['imageUrl']) && is_string($button['imageUrl']) )
      echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
    else
      echo CHtml::link($label, $url, $options);
  }

  /**
   * Renders the filter cell content.
   * This method will render the {@link filter} as is if it is a string.
   * If {@link filter} is an array, it is assumed to be a list of options, and a dropdown selector will be rendered.
   * Otherwise if {@link filter} is not false, a text field is rendered.
   * @since 1.1.1
   */
  protected function renderFilterCellContent()
  {
    if( $this->filter !== null )
    {
      if( is_string($this->filter) )
        echo $this->filter;
      else if( $this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos($this->name, '.') === false )
      {
        echo CHtml::activeLabelEx($this->grid->filter, $this->name, array('id' => false));

        if( is_array($this->filter) )
          echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, array('id' => false, 'prompt' => ''));
        else if( $this->filter === null )
          echo CHtml::activeTextField($this->grid->filter, $this->name, array('id' => false));
      }
      else
        parent::renderFilterCellContent();
    }
  }
}
