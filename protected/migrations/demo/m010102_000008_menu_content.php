<?php
Yii::import('frontend.components.auth.FUserIdentity');

class m010102_000008_menu_content extends CDbMigration
{

	public function up()
	{
    $this->execute("INSERT INTO `{{menu_custom_item}}` (`id`, `name`, `url`, `visible`) VALUES
      (1, 'О компании', '1', 1),
      (2, 'Доставка и оплата', '1', 1),
      (3, 'Вакансии', '1', 1),
      (4, 'Контакты', '/contact/', 1);");

    $this->execute("INSERT INTO `{{menu_item}}` (`id`, `menu_id`, `item_id`, `type`, `frontend_model`, `position`) VALUES
      (1, 1, 1, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (2, 1, 2, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (3, 1, 3, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (4, 1, 4, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (5, 2, 2, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (6, 2, 1, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (7, 2, 3, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10),
      (8, 2, 4, 'BFrontendCustomMenuItem', 'CustomMenuItem', 10);");
	}

	public function down()
	{
    $this->delete('{{menu_item}}', 'id IN (1, 2, 3, 4, 5, 6, 7, 8)');
    $this->delete('{{menu_custom_item}}', 'id IN (1, 2, 3, 4)');

		return true;
	}
}