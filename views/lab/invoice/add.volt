{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new invoice"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Generate new invoices"|t }}</h3>

    {{ form('lab/invoice/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Start date"|t }}:</label>
                    {{ text_field('start_date', 'class': 'form-control datepicker', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client name"|t }}:</label>
                    <select id="client_data" class="form-control select2-input" name="invoice_clients[]" multiple="multiple">
                        <option value="all">{{ "Select all dentists"|t }}</option>
                        {% for d in dentists %}
                            <option value="{{ d['dentist_data']['id'] }}">{{ d['dentist_data']['name'] }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "End date"|t }}:</label>
                    {{ text_field('end_date', 'class': 'form-control datepicker', 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Invoice date"|t }}:</label>
                    {{ text_field('date', 'class': 'form-control datepicker', 'required': 'required') }}
                </div>
                <div class="form-group">
                    <input type="hidden" name="invoice_type" value="lab" />
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Generate invoices"|t }}</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(document).ready(function () {
            var clients = [];

            $('#all_dentists').on('click', function(){
               if($(this).is(":checked")){
                   $("#client_data").attr("disabled", "disabled");
                   $(this).attr("checked", "checked");
                   $(this).val(1);
               }
               else {
                   $("#client_data").removeAttr("disabled");
                   $(this).removeAttr("checked");
                   $(this).val(0);
               }
            });

            $("#client_data").on('select2:select', function (e) {
                var data = e.params.data;
                $(this).children('option').each(function(){
                    if($(this).val() != 'all'){
                        clients.push($(this).val());
                    }
                });

                if(data.id == "all"){
                    $('#client_data').val(clients);
                    $('#client_data').trigger('change');
                }
            });
        });
    </script>

{% endblock %}