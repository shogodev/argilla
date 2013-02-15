<?php

class TextblockModule extends BModule
{
  public $defaultController = 'textBlock';

  public $name = 'Текстовые блоки';

	public function init()
	{
		$this->setImport(array(
			'textblock.models.*',
			'textblock.components.*',
			'textblock.controllers.*',
		));
	}
}
