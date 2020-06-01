{% extends "layouts/main.volt" %}
{% block title %} {{ "AVG"|t }} {% endblock %}
{% block content %}

    <iframe
            class="avgiframe"
            src="{{ currentOrganisation.getIso2hUrl() }}?styling={{ styling }}&username={{ username }}&password={{ password }}"
            width="100%"
    ></iframe>

{% endblock %}