<?php
/* @var $this BOrderController */
/* @var $model BOrder */
?>
<ul class="s-breadcrumbs breadcrumb">
  <li class="active">Товары</li>
</ul>

<?php $widgetId = 'products-grid';?>

<div class="s-buttons s-buttons-additional">
  <?php $this->widget('BAssignerButton', array(
    'type' => BButton::TYPE_INFO,
    'label' => 'Добавить',
    'assignerOptions' => array(
      'iframeUrl' => $this->createUrl('/product/product/index', array('popup' => 1, 'src' => 'BOrder', 'srcId' => $model->id)),
      'submitUrl' => $this->createUrl('/order/orderProduct/addProducts', array('orderId' => $model->id)),
      'updateGridId' => $widgetId,
      'addButton' => true
    ),
  ))?>
</div>

<?php
$onFlyAjaxUrl = Yii::app()->controller->createUrl('/order/orderProduct/onflyedit');
$this->widget('BGridView', array(
  'id' => $widgetId,
  'dataProvider' => new CArrayDataProvider($model->getProducts(), array('pagination' => false)),
  'template' => "{filters}\n{items}\n{pagesize}\n{pager}\n{scripts}",
  'rowCssClassExpression' => '$data instanceof BOrderProduct ? "group" : ($row % 2 ? "odd" : "even" )',
  'columns' => array(
    array('name' => 'fullName', 'header' => 'Название', 'type' => 'html'),
    array(
      'name' => 'price',
      'header' => 'Цена',
      'value' => 'PriceHelper::isNotEmpty($data->price) ? PriceHelper::price($data->price) : ""',
      'htmlOptions' => array('class' => 'span2'),
      'class' => 'BConditionDataColumn',
      'columns' => array(
        array('class' => 'BDataColumn'),
        array('class' => 'OnFlyEditField', 'ajaxUrl' => $onFlyAjaxUrl, 'gridUpdate' => true),
      ),
      'condition' => '($data instanceof BOrderProduct) ? 1 : 0;'
     ),
    array(
      'name' => 'discount',
      'header' => 'Скидка',
      'htmlOptions' => array('class' => 'span2'),
      'value' => 'PriceHelper::isNotEmpty($data->discount) ? floatval($data->discount) : ""',
      'class' => 'BConditionDataColumn',
      'columns' => array(
        array('class' => 'BDataColumn'),
        array('class' => 'OnFlyEditField', 'ajaxUrl' => $onFlyAjaxUrl, 'gridUpdate' => true),
      ),
      'condition' => '($data instanceof BOrderProduct) ? 1 : 0;'
    ),
    array(
      'name' => 'count',
      'header' => 'Количество',
      'value' => '!empty($data->count) ? $data->count : ""',
      'htmlOptions' => array('class' => 'span2'),
      'class' => 'BConditionDataColumn',
      'columns' => array(
        array('class' => 'BDataColumn'),
        array('class' => 'OnFlyEditField', 'ajaxUrl' => $onFlyAjaxUrl, 'gridUpdate' => true),
      ),
      'condition' => '($data instanceof BOrderProduct) ? 1 : 0;'
    ),
    array('name' => 'sum', 'header' => 'Сумма', 'value' => 'PriceHelper::isNotEmpty($data->sum) ? PriceHelper::price($data->sum) : ""'),
    array(
      'class' => 'BButtonColumn',
      'template' => '{selectParameter} {delete}',
      'deleteButtonUrl' => function ($data) {
        return $data instanceof BOrderProduct ? Yii::app()->controller->createUrl('/order/orderProduct/delete', array('id' => $data->id)) : Yii::app()->controller->createUrl('/order/orderProductItem/delete', array('id' => $data->id));
      },
      'buttons' => array(
        'selectParameter' => array(
          'label' => '',
          'url' => function ($data) {
            return Yii::app()->controller->createUrl('/order/orderProductItem/parameters', array('id' => $data->id, 'popup' => true, 'src' => 'BOrder'));
          },
	        'options'=> array(
            'class' => 'add js-add-order-product-item',
          ),
	        'visible' => 'isset($data->allowedAddParameter) && $data->allowedAddParameter',
        )
      )
    ),
  ),
)); ?>

<div class="s-buttons s-buttons-additional">
  <?php $this->widget('BButton', array(
    'type' => BButton::BUTTON_LINK,
    'icon' => 'icon-share-alt',
    'label' => 'Отправить уведомление о заказе',
    'url' => $this->createUrl('/order/order/sendNotification', array('orderId' => $model->id))
  ))?>
</div>

<script language="JavaScript">
  $(function() {
    jQuery.fn.yiiGridView.addObserver('<?php echo $widgetId?>', function(id, data) { $('#js-order-sum').replaceWith($('<div>' + data + '</div>').find('#js-order-sum')); });

    jQuery(document).on('click', '.js-add-order-product-item', function(e) {
      e.preventDefault();
      assigner.apply(this, {
        addButton : true,
        submitUrl : this.href,
        iframeUrl : this.href,
        multiSelect : false,
        updateGridId : '<?php echo $widgetId?>',
        width : 800,
        height : 400,
        left : '50%',
        top : '50%',
        marginTop : '-250px',
        marginLeft: '-400px'
      });
    });
  });
</script>