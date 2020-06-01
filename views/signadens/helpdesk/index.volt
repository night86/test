{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Helpdesk page editor"|t }}</h3>
    <form action="{{ url('signadens/helpdesk/save/') }}" method="post"
          enctype="multipart/form-data">
        <fieldset class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {#<label>{{ 'Helpdesk'|t }}</label>#}
                        <textarea id="helpdesk-text">
                        {% if content.html is not null %}
                        {{ content.html }}
                            {% endif %}

                        </textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="complete" class="btn btn-save-tpl pull-left"><i
                                    class="pe-7s-play complete-edit"></i> {{ "Save"|t }}</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        var save_notification = '{{ "Page saved"|t }}';
    </script>
{% endblock %}