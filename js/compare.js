////-------------------------------------------------
// Click functions
$(function(){
  //------------------------------------------------
  $('.to_compare').on('click', function(e){
    e.preventDefault();

    var id   = $(this).attr('data-product-id');
    var self = $(this);

    if( id == undefined )
      return false;

    var callback = function()
    {
      addCompareMessage(self);
    }

    $.post(window.config["urls"]["compare"]["add"] + id, '', callback);
  });

  //------------------------------------------------
  $('.from_compare').on('click', function(e){
    e.preventDefault();

    var id  = $(this).attr('data-product-id');

    if( id == undefined )
      return false;

    var callback = function()
    {
      document.location.reload();
    }

    $.post(window.config["urls"]["compare"]["remove"] + id, '', callback);
  });

  //------------------------------------------------
  $('.clear_compare').on('click', function(e){
    var callback = function()
    {
      document.location.reload();
    }

    $.post(window.config["urls"]["compare"]["clear"], '', callback);
  });

  //------------------------------------------------
  $('.clear_compare_group').on('click', function(e){
    e.preventDefault();

    var id = $(this).attr('data-compare-id');

    if( id == undefined )
      return false;

    var callback = function()
    {
      document.location.reload();
    }

    $.post(window.config["urls"]["compare"]["clearGroup"] + id, '', callback);
  });

  //------------------------------------------------
  $('#compare_popup_close').on('click', function(e){
    e.preventDefault();
    $('.popup').hide();
  });
});

//--------------------------------------------------
// Common
function addCompareMessage(t)
{
  updateCompareCount();
  var target = $(t).attr('class') + '_content';

  if($('#'+target).is(':hidden'))
  {
    $.showpos(t, target, {value:'under+103',auto:false}, {value:'left+479',auto:'right'}, true, false);
    $('#'+target).find('input:first').focus();
  }
  else
    $('#'+target).hide();
}

function updateCompareCount()
{
  var callback = function( resp )
  {
    $('.compare_count').html( resp );
  }

  $.post(window.config["urls"]["compare"]["count"], '', callback)
}
