<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class BFrontendMenuException extends CException
{
  const EMPTY_PROPERTIES        = 0;
  const WRONG_CLASS_INHERITANCE = 1;
  const MODEL_DOES_NOT_EXIST    = 2;
  const EMPTY_PARENT            = 3;
}