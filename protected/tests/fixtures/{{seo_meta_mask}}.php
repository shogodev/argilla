<?php
return array(
  // MetaTest
  'mask 1' => array('url_mask' => '/testUri/', 'title' => '{h1} mask page title', 'visible' => 1),
  'mask 2' => array('url_mask' => '/testVars/', 'title' => '{ProductSection:name}', 'visible' => 1),
  'mask 3' => array('url_mask' => '/testCommandsUpper/', 'title' => 'upper{ProductSection:name}', 'visible' => 1),
  'mask 4' => array('url_mask' => '/testCommandsLower/', 'title' => 'lower{ProductSection:name}', 'visible' => 1),
  'mask 5' => array('url_mask' => '/testCommandsUcfirst/', 'title' => 'ucfirst{ProductSection:name}', 'visible' => 1),
  'mask 6' => array('url_mask' => '/testOneModel/', 'title' => '{name}', 'visible' => 1),
  'mask 7' => array('url_mask' => '/testOverriding/', 'title' => 'Overrated title', 'visible' => 1),

  // MetaMaskTest
  'mask 8' => array('url_mask' => '#/\w+/price/[\d\-]+/#', 'visible' => 1),
);