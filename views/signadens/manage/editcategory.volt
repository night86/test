{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit product category"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/manage/indexcategory") }}"><i class="pe-7s-back"></i></a> {{ "Edit product category"|t }}</h3>

    {{ form('signadens/manage/editcategory/'~category.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{'Name'|t}}:</label>
                    {{ text_field('name', 'required': 'required', 'class': 'form-control', 'value': category.getName()) }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            {{ submit_button('Save'|t, 'class': 'btn btn-primary') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}