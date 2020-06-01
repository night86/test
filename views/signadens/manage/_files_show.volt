{% if isDelete IS NOT defined %}
    {% set isDelete = false %}
{% endif %}
{% if files is not null %}
    <table class="files-table">
    {% for file in files %}
        <tr>
            <td><a href="/signadens/manage/download/{{ file.getId() }}" class="btn btn-info" style="display: block;"> {{ file.getName() }} </a></td>
            {% if isDelete IS true %}
                <td><a href="/signadens/manage/filedelete/{{ file.getId() }}" class="btn btn-danger remove-file"><i class="pe-7s-trash"></i> {{ "Delete file"|t }}</a></td>
            {% endif %}
        </tr>
    {% endfor %}
    </table>
{% endif %}

{% block scripts %}
    <script>
        $(function () {
            $('.remove-file').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                var _this = $(this);

                $.ajax({
                    url: _this.attr('href'),
                    method: 'GET',
                    success: function() {
                        _this.parent().parent().remove();
                        alert('{{'File deleted'|t}}');
                    }
                });
            });
        });
    </script>
{% endblock %}
