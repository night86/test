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
                {{ form('supplier/import/index', 'method': 'post', 'enctype': 'multipart/form-data') }}
                <div class="col-lg-12">
                    <fieldset class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>{{ "Download an empty template here:"|t }} <a target="_blank" href="{{ url('attachments/Signadens_leeg_importbestand.xlsx') }}">{{ "download file template"|t }}</a></h5>
                                <h5>{{ "Download an example template including product categories here:"|t }} <a target="_blank" href="{{ url('attachments/Signadens_productcategorieen.xlsx') }}">{{ "download file template with data"|t }}</a> {{ "(download file template with data - additional info)"|t }}</h5>
                                <h5><strong>{{ "Please note: use a clean and clear import file"|t }}</strong></h5>
                            </div>
                            {{ hidden_field('importType', 'value': 'update') }}
                            {#<div class="col-md-12">#}
                                {#<div class="form-group">#}
                                    {#<h4>{{ "Select type of import"|t }}</h4>#}
                                    {#<div class="radio">#}
                                        {#<label>{{ radio_field('importType', 'required': 'required', 'value': 'update', 'class': 'radio-inline') }} {{ "Product updates"|t }}</label>#}
                                    {#</div>#}
                                    {#<div class="radio">#}
                                        {#<label>{{ radio_field('importType', 'required': 'required', 'value': 'create', 'class': 'radio-inline') }} {{ "Full product import"|t }}</label>#}
                                    {#</div>#}
                                {#</div>#}
                            {#</div>#}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h4>{{ "Select file for import"|t }}</h4>
                                    {{ file_field('importFile', 'required': 'required') }}
                                </div>
                            </div>
                            <div class = "col-md-12" style = "display:none;" id = "delimiter">
                                <div class="form-group">
                                    <h4>{{ "Select type of CSV delimiter: "|t }}</h4>
                                    <div class="radio">
                                        <label>{{ radio_field('delimiterType', 'required': 'required', 'value': 'semicolon', 'class': 'radio-inline', 'checked':'checked') }}<b>;</b> {{ " - Semicolon"|t }}</label>
                                    </div>
                                    <div class="radio">
                                        <label>{{ radio_field('delimiterType', 'required': 'required', 'value': 'comma', 'class': 'radio-inline') }} <b>,</b>{{ " - Comma"|t }}</label>
                                    </div>
                                    <div class="radio" style = "display:none;">
                                        <label>{{ radio_field('delimiterType', 'required': 'required', 'value': 'tab', 'class': 'radio-inline') }}<b>/t</b> {{ " - Tab"|t  }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <h4>{{ "Select changes commencing date"|t }}</h4>
                                <div class="row">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">

                                            <div class="radio">
                                                {{ text_field('importDate', 'required': 'required', 'class': 'datepicker-user-date form-control', 'style':'width:200px;') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-sm-6">
                                        {#{{ submit_button('Next step >', 'class': 'btn btn-primary pull-right') }}#}
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