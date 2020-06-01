{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit tariff code"|t }} {% endblock %}
{% block content %}

    <h3>
        <a href="{{ url("lab/sales_tariff/") }}"><i class="pe-7s-back"></i></a>
        {{ "Edit tariff code"|t }}
    </h3>

    {{ form('lab/sales_tariff/edit/' ~ code.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Ledger code"|t }}</legend>
        {{ hidden_field('id', 'value': code.getId()) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Code"|t }}:</label>
                    {{ numeric_field('code', 'required': 'required', 'value': code.getCode(), 'class': 'form-control', "disabled": "disabled") }}
                </div>
                {#<div class="form-group">#}
                    {#<label>{{ "Recipe"|t }}:</label>#}
                    {#{{ select('recipe_id', recipes, 'required': 'required', 'value': code.getRecipeId(), 'class': 'form-control') }}#}
                {#</div>#}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'value': code.getDescription(), 'class': 'form-control', 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Price"|t }}:</label>
                    {{ text_field('price', 'value': code.getPrice(), 'class': 'form-control', 'required': 'required') }}
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