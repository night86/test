{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}</h3>
    </div>

    <div class="import-steps">
        <div class="row">
            <div class="col-md-12">
                <ul class="step-list">
                    <li {% if router.getActionName() === 'index' %} class="active" {% endif %}>
                        1. {{ "Select import type and file"|t }}</li>
                    <li {% if router.getActionName() === 'map' %} class="active" {% endif %}>
                        2. {{ "Map columns"|t }}</li>
                    <li {% if router.getActionName() === 'overview' %} class="active" {% endif %}>
                        3. {{ "Import overview"|t }}</li>
                    <li {% if router.getActionName() === 'confirm' %} class="active" {% endif %}>
                        4. {{ "Affected rows"|t }}</li>
                    <li {% if router.getActionName() === 'complete' %} class="active" {% endif %}>
                        5. {{ "Ready for approval"|t }}</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p class="padding-15">
                    {#{{ sprintf("The amount of products in queue for approval is: %s. Once the import is approved the changes are visible for the Labs. You can follow the status of the import on the <a href='%s'>import log</a>."|t, quantity, url('supplier/importlog/')) }}#}
                    {{ "The amount of products in queue for approval is:"|t }} {{ quantity }}. {{ "Once the import is approved the changes are visible for the Labs. You can follow the status of the import on the"|t }} <a href='/supplier/importlog/'>{{ "import log"|t }}</a>.
                </p>
            </div>
        </div>
    </div>

{% endblock %}