{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new tariff code"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new tariff code"|t }}</h3>

    {{ form('signadens/tariff/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Code'|t}}:</label>
                    {{ numeric_field('code', 'required': 'required', 'class': 'form-control') }}
                </div>
                {#<div class="form-group">#}
                    {#<label>{{'Recipe'|t}}:</label>#}
                    {#<select name="recipe_id" id="recipe_id" class="form-control">#}
                        {#<option value="">{{ "No selected"|t }}</option>#}
                        {#{% for key, recipe in recipes %}#}
                            {#<option value="{{ key }}">{{ recipe }}</option>#}
                        {#{% endfor %}#}
                    {#</select>#}
                {#</div>#}

            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Description'|t}}:</label>
                    {{ text_field('description', 'class': 'form-control', 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Price'|t}}:</label>
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