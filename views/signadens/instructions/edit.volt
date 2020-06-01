{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Edit helpdesk"|t }}</h3>

    <legend>{{ "Start page - Dentists"|t }}</legend>
    <form method="post" action="">
        <div class="row">
            <div class="col-xs-12">
                <textarea name="start_page[dentist]" class="tinymce">{{ organisationTypes.getStartPage() }}</textarea>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-xs-12">
                <input type="submit" class="btn btn-primary pull-right" value="{{ "Save"|t }}" />
            </div>
        </div>
    </form>
{% endblock %}

{% block scripts %}
    {{ super() }}
<script>
    $(function() {
        tinymce.init({
            selector: '.tinymce',
            language_url: '/js/tinymce/langs/nl.js',
            plugins: ["link", "image"],
            height: 300,
            selection_toolbar: 'link bold italic | quicklink h2 h3 blockquote',
            menu: {}
        });
    });
</script>
{% endblock %}