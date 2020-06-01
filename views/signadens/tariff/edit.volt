{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit tariff code"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/tariff/") }}"><i class="pe-7s-back"></i></a> {{ "Edit tariff code"|t }}</h3>

    {{ form('signadens/tariff/edit/' ~ code.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        {{ hidden_field('id', 'value': code.getId()) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Code'|t}}:</label>
                    {{ numeric_field('code', 'required': 'required', 'value': code.getCode(), 'class': 'form-control', "disabled": "disabled") }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Description'|t}}:</label>
                    {{ text_field('description', 'value': code.getDescription(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Price'|t}}:</label>
                    {{ text_field('price', 'value': code.getPrice(), 'class': 'form-control') }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ "Lot number"|t }}</label>
                </div>
                <div class="form-group">
                    <input class="options_lot_type" type="radio" name="options[lot]" value="none" {% if options['lot'] == 'none' or options is null %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "None"|t }}&nbsp;&nbsp;
                    <input class="options_lot_type" type="radio" name="options[lot]" value="unique" {% if options['lot'] == 'unique' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Unique"|t }}&nbsp;&nbsp;
                    <input class="options_lot_type" type="radio" name="options[lot]" value="periodic" {% if options['lot'] == 'periodic' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Usage / Periodic"|t }}&nbsp;&nbsp;
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ "Batch number"|t }}</label>
                </div>
                <div class="form-group">
                    <input class="options_batch_type" type="radio" name="options[batch]" value="none" {% if options['batch'] == 'none' or options is null %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "None"|t }}&nbsp;&nbsp;
                    <input class="options_batch_type" type="radio" name="options[batch]" value="unique" {% if options['batch'] == 'unique' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Unique"|t }}&nbsp;&nbsp;
                    <input class="options_batch_type" type="radio" name="options[batch]" value="periodic" {% if options['batch'] == 'periodic' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Usage / Periodic"|t }}&nbsp;&nbsp;
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ "Alloy"|t }}</label>
                </div>
                <div class="form-group">
                    <input class="options_alloy_type" type="radio" name="options[alloy]" value="no" {% if options['alloy'] == 'no' or options is null %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "No"|t }}&nbsp;&nbsp;
                    <input class="options_alloy_type" type="radio" name="options[alloy]" value="yes" {% if options['alloy'] == 'yes' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Yes"|t }}&nbsp;&nbsp;
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>{{ "Design number"|t }}</label>
                </div>
                <div class="form-group">
                    <input class="options_design_type" type="radio" name="options[design]" value="no" {% if options['design'] == 'no' or options is null %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "No"|t }}&nbsp;&nbsp;
                    <input class="options_design_type" type="radio" name="options[design]" value="yes" {% if options['design'] == 'yes' %}checked="checked"{% endif %} />&nbsp;&nbsp;{{ "Yes"|t }}&nbsp;&nbsp;
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {% if code.code < 9000 %}
                        <label for="ledger_sales">{{ "Ledger code sales"|t }}</label>
                        <select id="ledger_sales" name="ledger_sales_id" class="form-control select2-input">
                            <option></option>
                            {% for ledger in ledgerCodes %}
                                <option {% if ledger.id == code.ledger_sales_id %}selected="selected"{% endif %} value="{{ ledger.id }}">{{ ledger.code }} - {{ ledger.description }}</option>
                            {% endfor %}
                        </select>
                    {% endif %}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {#<label>{{'Related Recipes'|t}}:</label>#}
                    {#{% for recipe in recipes %}#}
                        {#<div>- {{ recipe }}</div>#}
                    {#{% endfor %}#}
                </div>
            </div>
            <div class="col-md-4">
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