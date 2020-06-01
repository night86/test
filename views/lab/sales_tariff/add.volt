{% extends "layouts/main.volt" %}
{% block title %} {{ "Add tariff code"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add tariff code"|t }}</h3>

    {{ form('lab/sales_tariff/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Tariff code"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Code"|t }}:</label>
                    {{ numeric_field('code', 'required': 'required', 'class': 'form-control') }}
                </div>
                {#<div class="form-group">#}
                    {#<label>Recipe:</label>#}
                    {#{{ select('recipe_id', recipes, 'required': 'required', 'class': 'form-control') }}#}
                {#</div>#}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'class': 'form-control', 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Price"|t }}:</label>
                    {{ text_field('price', 'class': 'form-control', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </fieldset>
    {{ end_form() }}

{% endblock %}