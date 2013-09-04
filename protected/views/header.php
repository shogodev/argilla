<?php
/**
 * @var FController $this
 */
?>
  <header class="nofloat">
    <a href="<?php echo $this->createUrl('index/index')?>">
      <img src="i/logo.png" alt="" />
    </a>
    <div class="no-overflow" style="padding-bottom: 38px">
      <div class="menu hd-menu main-hd-menu fr">
        <?php $this->widget('FMenu', array('items' => $this->getTopMenu()))?>
      </div>
    </div>
    <div class="nofloat">
      <div class="fl">
        <div class="nofloat phones">
          <a href="" class="callback-link">
            <img src="i/btn-callback.png" alt="" />
          </a>
          <div class="popup" id="callback-popup">
            <a href="" class="close"></a>
            <div class="h2">Заказ обратного звонка</div>
            <?php echo $this->callbackForm?>
          </div>
          <script>
            //<[CDATA[
            $('.callback-link').click(function(e){
              e.preventDefault();
              var target = '#callback-popup',
                self = $(this);
              if ( $(target).is(':visible') ) {
                $(target).hide();
              } else {
                $.showpos(self, target, {value:'under+10',auto:false}, {value:'left-5',auto:false}, true, false);
              }
            })
            $('#callback-popup .close').click(function(e){
              e.preventDefault();
              var target = $(this).closest('.popup');
              target.hide();
            })
            //]]>
          </script>
        </div>
      </div>
  <div id="search" class="fbhandler-activate">
    <form method="get" action="/search/">
      <input class="inp" type="search" value="" name="search" title="Я ищу..." />
      <input type="image" alt="Поиск" src="i/sp.gif" />
    </form>
  </div>
  </header>
<script>
  $(function() {
    var urlPredictiveSearch = '<?php echo $this->createUrl('search/predictiveSearch')?>';

    $("input[name=search]").autocomplete({
      minLength: 2,
      delay: 300,
      search: '',
      select: function( event, ui ) {
        $("input[name=search]").val(ui.item.value);
        $("input[name=search]").parent('form').submit();
      }
    });

    $("input[name=search]").on('keyup', function() {
      var array = [];

      $("input[name=search]").autocomplete('option', 'source', array);

      if( $(this).val().length >= 2)
      {
        $.post(urlPredictiveSearch, {'query' : $(this).val()}, function(resp) {
          for(i in resp)
            array.push(resp[i]);
          $("input[name=search]").autocomplete('option', 'source', array);
        }, 'json');
      }

      $("input[name=search]").autocomplete('option', 'source', array);
    });
  });
</script>