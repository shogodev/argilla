<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('frontend.extensions.phpthumb.*');
Yii::import('frontend.extensions.upload.components.*');

class AbstractImagesImportCommand extends AbstractImportCommand
{
  public $importLogFile = 'import_images.log';
}