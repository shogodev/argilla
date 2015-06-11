<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SingleImageGrid extends ImageGrid
{
  public $template = "{items}\n{pager}";

  public $afterAjaxUpdate = "js:function() {
    var td = $('#' + this.ajaxUpdate[0]).parents('td');
    if( td.find('.items a').length == 0 ){
      td.find('.fileupload-files').show();
      td.find('.fileupload-buttonbar').show();
    }
  }";

  protected function initColumns()
  {
    $this->imageColumn();
    $this->buttonColumn();

    parent::initColumns();
  }
}