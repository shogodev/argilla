<?php
/**
 * @var ProductController $this
 * @var Product $model
 * @var FActiveDataProvider $similarDataProvider
 * @var FActiveDataProvider $relatedDataProvider
 * @var FActiveDataProvider $colorsDataProvider
 * @var array $_data_
 * @var Product $product
 */
?>

<div class="wrapper">
  <div class="m10">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>

  <?php ViewHelper::header($model->getHeader(), 'h1', array('class' => 'uppercase'))?>

  <div class="product-card nofloat m35">
    <div class="product-main-info">
      <div class="inner m30">
        <table class="zero short-details-table m10">
          <?php if( $section = $model->section ) { ?>
            <tr>
              <th>Раздел:</th>
              <td><?php echo $section->name?></td>
            </tr>
          <?php } ?>
          <?php if( $type = $model->type ) { ?>
          <tr>
            <th>Тип товара:</th>
            <td><?php echo $type->name?></td>
          </tr>
          <?php } ?>
          <?php if( $category = $model->category ) { ?>
          <tr>
            <th>Бренд:</th>
            <td><?php echo $category->name?></td>
          </tr>
          <?php } ?>
        </table>
        <div class="delivery-incut m20">
          <?php echo $this->textBlockRegister('Текст о доставке в карточке', 'Текст о доставке');?>
        </div>
        <div class="product-price-block">
          <?php if( $economy = PriceHelper::getEconomyPercent($model->getPriceOld(), $model->getPrice()) ) { ?>
            <div class="economy">- <?php echo $economy?>%</div>
          <?php } ?>
          <?php if( PriceHelper::isNotEmpty($model->getPriceOld()) ) { ?>
            <div class="old-price"><?php echo PriceHelper::price($model->getPriceOld())?></div>
          <?php } ?>
          <div class="price">
            <span class="price-label">Цена:</span> <?php echo PriceHelper::price($model->getPrice(), ' <span class="currency">руб.</span>')?>
          </div>
        </div>
        <div class="center">
          <?php if( $model->dump ) {?>
            <?php echo $this->basket->buttonFastOrder($model, 'Купить в 1 клик', array('class' => 'btn turq-btn h32btn p20btn s16 one-click-btn'), array(
              'url' => $model->getUrl(),
              'price' => PriceHelper::price($model->getPrice(), ' <span class="currency">руб.</span>'),
              'img' => $model->getImage()->pre,
              'name' => $model->name
            ))?>
            <?php echo $this->basket->buttonAdd($model, array('Купить', 'В корзине'), array('class' => 'btn blue-btn cornered-btn h32btn p40btn s19 uppercase buy-btn', 'href' => $this->createUrl('order/firstStep')))?>
          <?php } else {?>
            <a class="btn grey-btn cornered-btn h32btn s18 order-btn">Нет в наличии</a>
          <?php }?>
        </div>
      </div>
      <div class="social-likes social-likes_light center" data-counters="no">
        <div class="vkontakte" title="Поделиться в VK"></div>
        <div class="facebook" title="Поделиться в Facebook"></div>
        <div class="twitter" title="Поделиться в Twitter"></div>
        <!-- <div class="plusone" title="Поделиться в Google+"></div> -->
      </div>
    </div>

    <?php $this->renderPartial('one/_gallery', $_data_)?>

  </div>

  <?php $this->renderPartial('one/_tabs', $_data_)?>

</div>