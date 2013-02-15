var CALLBACKS = {};

function checkResponse(response, form)
{
  if( response === undefined || response === null )
    return;

  if( response.status == 'ok' )
  {
    if( response.updateElements !== undefined )
      updateElements(response.updateElements);

    if( response.hideElements !== undefined )
      hideElements(response.hideElements);

    if( response.showElements !== undefined )
      showElements(response.showElements);

    if( response.removeElements !== undefined )
      removeElements(response.removeElements);

    if( response.overlayLoader !== undefined )
      $.overlayLoader(true, response.overlayLoader);

    if( response.callbacks !== undefined )
      callCallbacks(response.callbacks)


    if( response.reload !== undefined )
      location.reload();

    if( response.redirect !== undefined )
      location.href = response.redirect;

    if( response.message !== undefined )
      alert(response.message);
  }

  if( response.status == 'ok' &&  form !== undefined )
  {
    if( response.validateErrors !== undefined )
      show_yii_errors_messages(form, $.parseJSON(response.validateErrors))

    if( response.messageForm !== undefined )
    {
      form.html('<div id=\'form_success_message\'>' + response.messageForm + '</div>');
      scrollTo('#form_success_message');
    }
  }

  $.mouseLoader(false);
}


function updateElements(data)
{
  for(var i in data)
  {
    if( data.hasOwnProperty(i) )
    {
      selector = '.' + i + ',#' + i;

      if( $(selector).is('input') )
        $(selector).val(data[i]);
      else if( $('.' + i).hasClass('html') )
        $(selector).html(data[i]);
      else
        $(selector).text(data[i]);
    }
  }
}

function hideElements(data)
{
  for(var i in data)
  {
    if( data.hasOwnProperty(i) )
      if( $('.' + data[i]).length )
        $('.' + data[i]).hide();
  }
}

function showElements(data)
{
  for(var i in data)
  {
    selector = '.' + data[i] + ',#' + data[i];

    if( data.hasOwnProperty(i) )
      if( $(selector).length )
        $(selector).show();
  }
}

function removeElements(data)
{
  for(var i in data)
  {
    if( data.hasOwnProperty(i) )
      if( $('.' + data[i]).length )
        $('.' + data[i]).remove();
  }
}

function callCallbacks(data)
{
  for(i in data)
  {
    if( CALLBACKS && CALLBACKS[i] !== undefined )
      CALLBACKS[i](data[i]);
  }
}

function show_yii_errors_messages(form, messages)
{
  var data = form.data();

  form.yiiactiveform.updateSummary(form, messages);

  $.each(data.settings.attributes, function ()
  {
    form.yiiactiveform.updateInput(this, messages, form);
    delete messages[this.inputID];
  });

  var error = [];
  for(var i in messages)
    if(messages.hasOwnProperty(i))
      error.push(messages[i].join("\n"));

  if(error.length)
    alert(error.join("\n"));
}

function scrollTo(selector)
{
  var offset = $(selector).offset().top - $('#global-nav').height() - $('#anchor-links-block').height() * 2;
  if ( $('#anchor-links-block').hasClass('fixed') )
    offset += $('#anchor-links-block').height();

  $(jQuery.browser.webkit ? document.body : 'html').animate({scrollTop: offset}, 100);
}