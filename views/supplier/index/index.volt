{% extends "layouts/main.volt" %}
{% block title %} {{'Dashboard'|t}} {% endblock %}
{% block content %}

<h3>{{ "Dashboard"|t }}</h3>

    <div class="row">
        <div class="col-md-12 col-sm-12">
        {% if currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_IMPORT_PRODUCTS') or currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_IMPORT_NOTIFICATIONS') %}
            <h4>{{ "Import"|t }}</h4>
            {{ partial("supplier/index/_import") }}
        {% endif %}

        {% if currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_SALES_NEW') %}
            <h4>{{ "Sales"|t }}</h4>
            {{ partial("supplier/index/_sales") }}
        {% endif %}

        {% if currentUser.hasRole('ROLE_SUPPLIER_DASHBOARD_MANAGEMENT_USERS') %}
            <h4>{{ "Application management"|t }}</h4>
            {{ partial("supplier/index/_management") }}
        {% endif %}
        </div>
    </div>

{% endblock %}