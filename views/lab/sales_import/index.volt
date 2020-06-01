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
                    {{ form('lab/sales_import/', 'method': 'post', 'enctype': 'multipart/form-data') }}
                    <div class="col-lg-12">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h4>{{ "Select type of import"|t }}</h4>
                                        <div class="radio">
                                            <label>{{ radio_field('importType', 'required': 'required', 'value': 'tariff', 'class': 'radio-inline') }} {{ "Tariff codes with ledger number sales"|t }}</label>
                                        </div>
                                        <div class="radio">
                                            <label>{{ radio_field('importType', 'required': 'required', 'value': 'ledger', 'class': 'radio-inline') }} {{ "Ledger codes (categories)"|t }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h4>{{ "Select file for import"|t }}</h4>
                                        {{ file_field('importFile', 'required': 'required') }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <button type="submit" class="btn btn-primary pull-right">{{ "Next step"|t }} <i class="pe-7s-angle-right"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </fieldset>
                    </div>
                    {{ end_form() }}
                </div>
            </div>
        </div>

{% endblock %}