if (jQuery.fn.yiiactiveform)
{
  jQuery.fn.yiiactiveform.addFields = function(form, fields)
  {
    var $s = form.data('settings');
    if( $s === undefined )
      return;

    fields.each(function()
    {
      var $field = $(this), has = false;
      jQuery.each($s.attributes, function (i, o)
      {
        if (o.id == $field.attr('id'))
        {
          has = true;
          return false;
        }
      });

      if( !has )
      {
        $s.attributes[$s.attributes.length] = jQuery.extend({
          validationDelay:$s.validationDelay,
          validateOnChange:$s.validateOnChange,
          validateOnType:$s.validateOnType,
          hideErrorMessage:$s.hideErrorMessage,
          inputContainer:$s.inputContainer,
          errorCssClass:$s.errorCssClass,
          successCssClass:$s.successCssClass,
          beforeValidateAttribute:$s.beforeValidateAttribute,
          afterValidateAttribute:$s.afterValidateAttribute,
          validatingCssClass:$s.validatingCssClass
        }, {
          id:$field.attr('id'),
          inputID:$field.attr('id'),
          errorID:$field.attr('id') + '_em_',
          model:$field.attr('name').split('[')[0],
          name:$field.attr('name'),
          enableAjaxValidation:true,
          status:1,
          value:$field.val()
        });

        form.data('settings', $s);
      }
    });
  };
}