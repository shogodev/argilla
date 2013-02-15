<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td style="width:6.5%"></td>
            <td style="width:29.0598%;padding:8px 16px" class="name"><span class="text-crop" style="width:100%">{%=file.name%}</span></td>
            <td class="size span2 nowrap"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview span1 center">
            {% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}
            </td>
            <td style="width:29.0598%;padding:8px 16px" class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}" class="text-crop" style="width:100%">{%=file.name%}</a>
            </td>
            <td class="size span2 nowrap"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="button-column">
            <span class="delete">
                <button class="delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" rel="tooltip" title="{%=locale.fileupload.destroy%}"></button>
            </span>
        </td>
    </tr>
{% } %}
</script>