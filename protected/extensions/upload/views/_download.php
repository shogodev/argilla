<!-- The template to display files available for download -->

<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  {% if (file.error) { %}
    <tr class="template-download fade">
      <td style="width:6.5%"></td>
      <td style="width:29.0598%;padding:8px 16px" class="name"><span class="text-crop" style="width:100%">{%=file.name%}</span></td>
      <td class="size nowrap"><span>{%=o.formatFileSize(file.size)%}</span></td>
      <td class="error" colspan="2"><span class="label label-important">{%=file.error%}</span></td>
      <td class="button-column"></td>
    </tr>
  {% } else if(0) { %}
    <tr class="template-download fade">
      <td style="width:6.5%" class="preview center">
      {% if (file.thumbnailUrl) { %}
          <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnailUrl%}"></a>
      {% } %}
      </td>
      <td style="width:29.0598%;padding:8px 16px" class="name">
          <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnailUrl%}" download="{%=file.name%}" class="text-crop" style="width:100%">{%=file.name%}</a>
      </td>
      <td class="size nowrap"><span>{%=o.formatFileSize(file.size)%}</span></td>
      <td colspan="2"></td>
      <td class="button-column">
          <span class="delete">
              <button class="delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %} rel="tooltip" title="Удалить"></button>
          </span>
      </td>
    </tr>
  {% } %}
    </tr>
{% } %}
</script>
