{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Dashboard"|t }}</h3>

    {#
    {{ partial("signadens/index/_indexAdmin") }}
    #}
    {#{% if 'admin' in currentUser.roleTemplate.name|lower %}#}
        {#{{ partial("signadens/index/_indexAdmin") }}#}
    {#{% else %}#}
        {#{{ partial("signadens/index/_indexEmployee") }}#}
    {#{% endif %}#}

{% endblock %}