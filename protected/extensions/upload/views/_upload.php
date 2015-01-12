<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td style="width:6.5%" class="preview center"><span class="fade"></span></td>
        <td style="width:29.0598%;padding:8px 16px" class="name">
          <span class="text-crop" style="width:100%">{%=file.name%}</span>
        </td>
        <td style="width:6.5%" class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
          <td colspan="2">
              <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
          </td>
        {% } %}
        <td class="button-column">
            {% if (!i && !o.options.autoUpload ) { %}
              <span class="start">
                  <button class="add" rel="tooltip" title="Начать"></button>
              </span>
            {% } %}
            {% if (!i) { %}
               <span class="cancel">
                    <button class="delete" rel="tooltip" title="Отменить"></button>
                </span>
            {% } %}
        </td>
    </tr>
{% } %}
</script>