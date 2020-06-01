{% extends "emailTemplates/layouts/main.volt" %}

{% block title %}{{ "New password"|t }}{% endblock %}

{% block content %}

{{ "New password has been generated"|t }} - {{ password }}

{% endblock %}