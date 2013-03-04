<section id="main">

  <?php $this->renderPartial('/breadcrumbs');?>
  <h2 class="m7">Страница не найдена</h2>

  <div class="error">
    <p class="red"><b>Извините, но запрошенная Вами страница не найдена.</b></p>
    <p>Возможно она была удалена с сервера или никогда не существовала.<br />Возможно Вы ошиблись при вводе url-адреса страницы.</p>
  </div>

  <?php echo CHtml::encode($message); ?>

</section>