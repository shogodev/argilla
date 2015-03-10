<?php

class m010102_000005_rotator_content extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{banner}}` (`id`, `position`, `location`, `title`, `url`, `img`, `swf_w`, `swf_h`, `code`, `pagelist`, `pagelist_exc`, `new_window`, `visible`) VALUES
      (1, 10, 'rotator', 'Ротатор 1', '', 'rotator1.jpg', NULL, NULL, '', '', '', 0, 1),
      (2, 20, 'rotator', 'Ротатор 2', '', 'rotator2.jpg', NULL, NULL, '', '', '', 0, 1),
      (3, 30, 'rotator', 'Ротатор 3', '', 'rotator3.jpg', NULL, NULL, '', '', '', 0, 1),
      (4, 40, 'rotator', 'Ротатор 4', '', 'rotator4.jpg', NULL, NULL, '', '', '', 0, 1);");
	}

	public function down()
	{
		$this->delete('{{banner}}', 'id IN (1, 2, 3, 4)');
		return true;
	}
}