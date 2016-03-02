Как подключать.

1) Скопировать protected/extensions/retailcrm/retail_crm.php.sample в protected/config/ retail_crm.php и настроить.

2) Добавить в frontend.php:

  'components' => array(
    ....
    'retailCrm' => array(
      'class' => 'ext.retailcrm.RetailCrm',
    ),
  ),

  'controllerMap' => array(
    'retailCrm' => 'ext.retailcrm.controllers.RetailCrmController'
  ),

3) Добавить в routes.php:
   // retailCrm
   'retailCrm' => array('retailCrm/icml', 'pattern' => 'retail_crm', 'urlSuffix' => '.icml'),

4) Выполнить миграцию:
    protected/yiic migrate up --migrationPath=frontend.extensions.retailcrm.migrations

5) Дописать в backend/protected/modules/order/views/order/_form.php и в backend/protected/modules/form/views/callback/_form.php код отображения ссылок на retailCrm:
    <?php if( !empty($model->retail_crm_url) ) echo $form->contentRow($model, 'retail_crm_url', CHtml::link($model->retail_crm_url, $model->retail_crm_url, array('target' => '_blank'))); ?>
