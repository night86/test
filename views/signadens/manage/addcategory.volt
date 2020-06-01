{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new product category"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new product category"|t }}</h3>

    {{ form('signadens/manage/addcategory', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Category"|t }}</legend>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{'Name'|t}}:</label>
                    {{ text_field('name', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="row">
                    <div class="col-lg-12">
                        {{ submit_button('Add'|t, 'class': 'btn btn-primary') }}
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}