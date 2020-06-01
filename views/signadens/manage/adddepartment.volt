{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new user department"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new user department"|t }}</h3>

    {{ form('signadens/manage/adddepartment', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Department"|t }}</legend>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{'Name'|t}}:</label>
                    {{ text_field('name', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary" type="submit"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}