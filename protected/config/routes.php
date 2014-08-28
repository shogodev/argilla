<?php
return array(

  // index
  'index' => array('index/index', 'pattern' => '/'),

  // search
  'search' => array('search/index', 'pattern' => 'search/<page:\d+>', 'defaultParams' => array('page' => 1)),

  // xml
  'xmlExport' => array('xmlExport/index', 'pattern' => '<id:\w+>_xml'),

  // info
  'info' => array('info/index', 'pattern' => 'info/<url:[\w\-]+>', 'urlSuffix' => '.html'),

  // contacts
  'contact' => array('contact/index', 'pattern' => 'contact'),

  // compare
  'compare'           => array('compare/index',      'pattern' => 'compare'),
  'compareAdd'        => array('compare/add',        'pattern' => 'compare/add'),

  'compareRemove'     => array('compare/remove',     'pattern' => '<url:compare\/remove>/<id:\d+>'),
  'compareClear'      => array('compare/clear',      'pattern' => 'compare/clear'),
  'compareClearGroup' => array('compare/clearGroup', 'pattern' => '<url:compare\/clear_group>/<id:\d+>'),

  // user
  'userRegistration' => array('user/registration', 'pattern' => 'user/registration', 'shouldRemember' => false),
  'userLogin' => array('user/login', 'pattern' => 'user/login', 'shouldRemember' => false),
  'userLogout' => array('user/logout', 'pattern' => 'user/logout', 'shouldRemember' => false),
  'userRestoreConfirmed' => array('user/restoreConfirmed', 'pattern' => 'user/restore/<code:\w+>', 'shouldRemember' => false),
  'userRestore' => array('user/restore', 'pattern' => 'user/restore', 'shouldRemember' => false),

  // userProfile
  'userProfile' => array('userProfile/profile', 'pattern' => 'user', 'shouldRemember' => false),
  'userProfileData' => array('userProfile/data', 'pattern' => 'user/data', 'shouldRemember' => false),
  'userProfileChangePassword' => array('userProfile/changePassword', 'pattern' => 'user/change_password', 'shouldRemember' => false),
  'userProfileHistoryOrders' => array('userProfile/historyOrders', 'pattern' => 'user/orders/history', 'shouldRemember' => false),
  'userProfileCurrentOrders' => array('userProfile/currentOrders', 'pattern' => 'user/orders/current', 'shouldRemember' => false),

  // forms
  'callback' => array('callback/index', 'pattern' => 'callback'),
  'response' => array('response/add', 'pattern' => 'add_response'),

  // basket
  'basket'          => array('basket/index',     'pattern' => 'basket'),
  'basketAdd'       => array('basket/add',       'pattern' => 'basket/add'),
  'basketCheckOut'  => array('basket/checkout',  'pattern' => 'basket/check_out'),
  'basketSuccess'   => array('basket/success',   'pattern' => 'basket/success'),
  'basketFastOrder' => array('basket/fastOrder', 'pattern' => 'basket/fastorder'),

  // payment system
  'paymentCheck'   => array('payment/check',   'pattern' => 'payment/check'),
  'paymentResult'  => array('payment/result',  'pattern' => 'payment/result'),
  'paymentSuccess' => array('payment/success', 'pattern' => 'payment/success'),
  'paymentFailure' => array('payment/failure', 'pattern' => 'payment/failure'),
  'paymentCapture' => array('payment/capture', 'pattern' => 'payment/capture'),

  // favorite
  'favorite' => array('favorite/index', 'pattern' => 'favorite'),

  // visits
  'visits' => array('visits/index', 'pattern' => 'visits'),

  // products
  'productFastOrder' => array('product/fastOrder', 'pattern' => 'fastorder/<id:\d+>'),
  'productSelection' => array('product/selection', 'pattern' => 'selection'),
  'productCategories' => array('product/categories', 'pattern' => 'categories'),
  'productSections'  => array('product/sections',  'pattern' => 'sections'),

  // db routing
  'productOne' => array('product/one', 'pattern' => '<url:\w+>', 'models' => array('url' => 'Product'), 'class' => 'DBRule'),
  'productSection' => array('product/section', 'class' => 'DBRule', 'pattern' => '<section:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1), 'models' => array('section' => 'ProductSection')),
  'productCategory' => array('product/category', 'class' => 'DBRule', 'pattern' => '<category:\w+>', 'models' => array('category' => 'ProductCategory')),
  'productType' => array('product/type', 'class' => 'DBRule', 'pattern' => '<type:\w+>/<page:\d+>', 'models' => array('type' => 'ProductType'), 'defaultParams' => array('page' => 1)),

  // news
  'newsSection' => array('news/section', 'class' => 'DBRule', 'pattern' => '<section:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1), 'models' => array('section' => 'NewsSection')),
  'newsOne'     => array('news/one', 'pattern' => '<section:(news|actions|video|articles)>/<url:\w+>', 'urlSuffix' => '.html'),

  // link
  'linkIndex' => array('link/index', 'pattern' => 'resources/<page:\d+>', 'defaultParams' => array('page' => 1)),
  'linkSection' => array('link/section', 'pattern' => 'resources/<section:\w+>/<page:\d+>', 'defaultParams' => array('page' => 1)),
  'linkAdd' => array('link/add', 'pattern' => 'links/add'),

  // captcha
  'captchaImg' => array('index/captcha', 'pattern' => '<model:\w+>/captcha/v/<code:\w+>'),
  'captchaRef' => array('index/captcha', 'pattern' => '<model:\w+>/captcha/<refresh:refresh>/<code:\w+>'),
);