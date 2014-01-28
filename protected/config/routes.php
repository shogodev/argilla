<?php
return array(

  // index
  'index' => array('index/index', 'pattern' => '/'),

  // search
  'search' => array('search/index', 'pattern' => 'search/<page:\d+>', 'defaultParams' => array('page' => 1)),

  // xml
  'xmlExport' => array('xmlExport/index', 'pattern' => '<id:\w+>_xml'),

  // news
  'newsSection' => array('news/section', 'pattern' => '<url:(news|articles|reviews)>/<page:\d+>', 'defaultParams' => array('page' => 1)),
  'newsOne'     => array('news/one',     'pattern' => 'news/<url:\w+>.html'),

  // info
  'info' => array('info/index', 'pattern' => 'info/<url:\w+>.html'),

  // contacts
  'contact' => array('contact/index', 'pattern' => 'contact'),

  // compare
  'compare'           => array('compare/index',      'pattern' => 'compare'),
  'compareAdd'        => array('compare/add',        'pattern' => 'compare/add'),

  'compareRemove'     => array('compare/remove',     'pattern' => '<url:compare\/remove>/<id:\d+>'),
  'compareClear'      => array('compare/clear',      'pattern' => 'compare/clear'),
  'compareClearGroup' => array('compare/clearGroup', 'pattern' => '<url:compare\/clear_group>/<id:\d+>'),

  // users
  'userRegistration' => array('user/registration',     'pattern' => 'user/registration'),
  'userLogin'        => array('user/login',            'pattern' => 'user/login'),
  'userLogout'       => array('user/logout',           'pattern' => 'user/logout'),
  'userRestoreCode'  => array('user/restoreConfirmed', 'pattern' => 'user/restore/<code:\w+>'),
  'userRestore'      => array('user/restore',          'pattern' => 'user/restore'),
  'userData'         => array('user/data',             'pattern' => 'user/data'),
  'userHistoryOne'   => array('user/historyOne',       'pattern' => 'user/history/<id:\d+>'),
  'userHistory'      => array('user/history',          'pattern' => 'user/history'),

  // forms
  'callback' => array('callback/index', 'pattern' => 'callback'),
  'response' => array('response/add', 'pattern' => 'add_response'),

  // basket
  'basket'          => array('basket/index',     'pattern' => 'basket'),
  'basketAdd'       => array('basket/add',       'pattern' => 'basket/add'),
  'basketCheckOut'  => array('basket/checkout',  'pattern' => 'basket/check_out'),
  'basketSuccess'   => array('basket/success',   'pattern' => 'basket/success'),
  'basketFastOrder' => array('basket/fastOrder', 'pattern' => 'basket/fastorder'),

  // favorite
  'favorite' => array('favorite/index', 'pattern' => 'favorite'),

  // visits
  'visits' => array('visits/index', 'pattern' => 'visits'),

  // products
  'productFastOrder' => array('product/fastOrder', 'pattern' => 'fastorder/<id:\d+>'),
  'productSelection' => array('product/selection', 'pattern' => 'selection'),
  'productSections'  => array('product/sections',  'pattern' => 'sections'),
  'productType'      => array('product/type',      'pattern' => 'type/<type:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1)),

  // db routing
  'productOne' => array('product/one', 'pattern' => '<url:\w+>', 'models' => array('url' => 'Product'), 'class' => 'DBRule'),
  'productSection' => array('product/section', 'class' => 'DBRule', 'pattern' => '<section:\w+>/<page:\d+|all>', 'defaultParams' => array('page' => 1), 'models' => array('section' => 'ProductSection')),

  // link
  'linkIndex' => array('link/index', 'pattern' => 'links/<page:\d+>', 'defaultParams' => array('page' => 1)),
  'linkSection' => array('link/section', 'pattern' => 'links/<section:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1)),
  'linkAdd' => array('link/add', 'pattern' => 'links/add'),

  // captcha
  'captchaImg' => array('index/captcha', 'pattern' => '<model:\w+>/captcha/v/<code:\w+>'),
  'captchaRef' => array('index/captcha', 'pattern' => '<model:\w+>/captcha/<refresh:refresh>/<code:\w+>'),
);