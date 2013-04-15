<?php

class m010101_000007_init_grid_settings extends CDbMigration
{
	public function up()
	{
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (1, 10, 'position', '', 'OnFlyEditField', '', 0, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (2, 20, 'name', '', 'BEditColumn', '', 1, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (3, 40, 'price', '', 'OnFlyEditField', '', 0, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (4, 30, 'articul', '', 'BEditColumn', '', 1, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (6, 50, 'section', 'Раздел', 'BProductAssignmentColumn', '', 1, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (7, 70, 'type', 'Тип', 'BProductAssignmentColumn', '', 1, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (8, 90, 'visible', '', 'JToggleColumn', '', 1, 1);");
    $this->execute("INSERT INTO `{{settings_grid}}` (`id`, `position`, `name`, `header`, `class`, `type`, `filter`, `visible`) VALUES (9, 80, 'BProduct', 'Продукты', '', '', 0, 1);");
	}

	public function down()
	{
		echo "m010101_000007_init_grid_settings does not support migration down.\n";
		return false;
	}
}