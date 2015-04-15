<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FormLayouts
{
  const FORM_LAYOUT = "{title}\n{elements}\n{description}\n<div class=\"form-submit\">{buttons}</div>\n";

  const ELEMENTS_LAYOUT = '<div class="form-row m20">{label}<div class="form-field">{input}{hint}{error}</div></div>';
}