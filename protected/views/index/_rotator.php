<?php
/**
 * @var IndexController $this
 * @var Banner[]|array $banners
 */
?>

<?php if( !empty($banners) ) { ?>
  <div class="rotator-wrapper m15">
    <div id="main-rotator" class="cycle-slideshow"
         data-cycle-slides="> a"
         data-cycle-speed="500"
         data-cycle-timeout="5000"
         data-cycle-pager-template="<a href=#><span></span></a>"
         data-cycle-pager-event="mouseover"
         data-cycle-update-view="1">
      <?php foreach($banners as $banner) { ?>
        <a href="<?php echo $banner->url?>"><img src="<?php echo $banner->image?>" alt="<?php echo $banner->title?>" /></a>
      <?php }?>
      <div class="cycle-pager"></div>
    </div>
  </div>
  <script type="text/javascript">
    //<![CDATA[
    $(function() {
      $('#main-rotator .cycle-pager > a').each(function(index){
        var banner = $('#main-rotator > a:eq(' + index + ')'),
          text = banner.find('img').attr('alt'),
          link = banner.attr('href');
        $(this).find('span').text( text ).after( $('<br />') );
        $(this).attr('href', link);
        $(this).click(function(){
          location.href = $(this).attr('href');
        })
      })
    });
    //]]>
  </script>
<?php }?>