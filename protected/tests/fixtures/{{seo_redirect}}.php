<?php
return array(
  '1' => ['id' => 1, 'base' => '/lyzhi_gornye/', 'target' => '/snoubordy/', 'type_id' => 1, 'visible' => 1],
  '2' => ['id' => 2, 'base' => '#/komplekty/(\d*)#', 'target' => '/aksessuary_dlya_lyzh/$1', 'type_id' => 1, 'visible' => 1],
  '3' => ['id' => 3, 'base' => '#/snoubordy/(\d*)(/?)(.*)#', 'target' => '/aksessuary_dlya_lyzh/$1$2$3', 'type_id' => 1, 'visible' => 1],

  '4' => ['id' => 4, 'base' => '/palki/', 'target' => '/elki_palki/', 'type_id' => 301, 'visible' => 1],
  '5' => ['id' => 5, 'base' => '/figurnye/', 'target' => '', 'type_id' => 404, 'visible' => 1],
  '6' => ['id' => 6, 'base' => '#/lyzhnoe_snaryazhenie/(\d*)#', 'target' => '/aksessuary_dlya_lyzh/$1', 'type_id' => 1, 'visible' => 1],
  '7' => ['id' => 7, 'base' => '/lyzhi_begovie/', 'target' => '/begovie/', 'type_id' => 1, 'visible' => 1],
  '8' => ['id' => 8, 'base' => '/argilla/', 'target' => '/', 'type_id' => 301, 'visible' => 1],
);