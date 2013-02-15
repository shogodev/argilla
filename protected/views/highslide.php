<script type="text/javascript">
  hs.graphicsDir = 'i/gallery/';
  hs.creditsText = '';
  hs.creditsHref = '';
  hs.outlineType = 'rounded-white';
  hs.outlineWhileAnimating = true;
  hs.enableKeyListener = false;
  // жесткое позиционирование всех элементов галлереии применяется если значения не ноль подключать вэтом случае highslide.full.js
  hs.xpos = 0;
  hs.ypos = 0;
  hs.registerOverlay({
    thumbnailId: null,
    hideOnMouseOut: true,
    overlayId: 'controlbar',//controlbar -включение панели управления на фото ; closebutton - включение кнопки закрыть.
    position: 'top right', //позицианирование панели управления или кнопки закрыть
    fade: 2 //для срабатывания панели управления либо кнопки закрыть в IE
  });
</script>
<!-- close button -->
<div id="closebutton" class="highslide-overlay closebutton" onclick="return hs.close(this)" title="Close"></div>
<!-- /close button -->
<!-- control bar -->
<div id="controlbar" class="highslide-overlay controlbar">
  <a href="#" class="previous" onclick="return hs.previous(this)" title="Назад"></a>
  <a href="#" class="next" onclick="return hs.next(this)" title="Вперед"></a>
  <a href="#" class="highslide-move" onclick="return false" title="Переместить"></a>
  <a href="#" class="close" onclick="return hs.close(this)" title="Закрыть"></a>
</div>
<!-- /control bar -->