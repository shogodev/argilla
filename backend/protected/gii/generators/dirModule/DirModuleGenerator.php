<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('application.gii.components.*');
Yii::import('application.gii.generators.dirModule.*');

class DirModuleGenerator extends CCodeGenerator
{
  public $codeModel = 'backend.gii.generators.dirModule.models.DirModule';
} 