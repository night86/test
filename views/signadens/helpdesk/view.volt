{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Helpdesk"|t }}</h3>
    {{ content.html }}
{% endblock %}