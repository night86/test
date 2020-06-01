{% extends "layouts/main.volt" %}
{% block title %} {{ "Add organisation"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new organisation"|t }}</h3>

    {{ form('signadens/organisation/add', 'method': 'post', 'enctype': 'multipart/form-data') }}
    {#{{ submit_button('Add', 'class': 'btn btn-primary pull-right') }}#}

    <fieldset class="form-group">

        <legend>{{ "Basic data"|t }}</legend>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Name"|t }}:</label>
                    {{ text_field('organisation[name]', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Street and number"|t }}:</label>
                    {{ text_field('organisation[address]', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Zipcode"|t }}:</label>
                    {{ text_field('organisation[zipcode]', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label for="name">{{'Image'|t}}</label>
                    <input type="file" name="logo" class="form-control" required>
                </div>

            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "City"|t }}:</label>
                    {{ text_field('organisation[city]', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Country"|t }}:</label>
                    {{ select('organisation[country_id]', countries, 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('organisation[telephone]', 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "General emailaddress"|t }}:</label>
                    {{ text_field('organisation[email]', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ 'Organisation type'|t }}:
                        <div id="group_label">
                            <label for="is_group">{{ 'Dentist group'|t }}:</label>
                            <input type="checkbox" name="organisation[is_group]" id="is_group" value="1" >
                        </div>
                    </label>
                    {#{{ select('organisation[organisation_type_id]', organisationTypes, 'required': 'required', 'class': 'form-control') }}#}
                    <select id="select_organisation" name="organisation[organisation_type_id]" class="form-control" required="required">
                        {% for index, organisationType in organisationTypes %}
                            <option value="{{ index }}">{{ organisationType|t }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label>{{'Active'|t}}:</label>
                    {#{{ select('organisation[active]', active, 'required': 'required', 'class': 'form-control') }}#}
                    <select name="organisation[active]" class="form-control" required="required">
                        {% for value, name in active %}
                            <option value="{{ value }}">{{ name|t }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>

            {# Supplier info #}
            <div class="col-md-4" id="supplier_info">
                <div class="form-group">
                    <h4>{{ "Type of supplier"|t }}</h4>
                    <div class="radio">
                        <label><input type="radio" name="supplier_info[type]" class="radio-inline" value="LOWEST_PRICE"> {{ "Lowest price"|t }}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="supplier_info[type]" class="radio-inline" value="DISCOUNT_RATE"> {{ "Discount rate in framework agreement"|t }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <h4>{{ "Shipping costs"|t }}</h4>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" value="free_shipping" name="supplier_info[shipping_costs]"> {{ "Free shipping"|t }}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" id="shippingLess" value="shipping_less" name="supplier_info[shipping_costs]"> {{ "Shipping costs will be charged when order is < €"|t }}
                            {#<div class="form-group">#}
                            <input type="number" name="supplier_info[shipping_less_than]" id="shippingLessThan" class="form-control numeric">
                            {#</div>#}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" value="shipping_costs" name="supplier_info[shipping_costs]"> {{ "Additional shipping costs will be charged"|t }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <h4>{{ "Delivery time"|t }}</h4>
                    <div class="form-group">
                        <label>{{ "Delivery time in workdays"|t }}:</label>
                        <input type="text" name="supplier_info[delivery_workdays]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ "When ordered before"|t }}:</label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="timepicker" name="supplier_info[delivery_time]" type="text" class="form-control input-small">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
            </div>

        </div>
    </fieldset>

    {{ end_form() }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            var select = $('#select_organisation');
            select.on('change', function () {
                if(select.val() == 3){
                    groupLabel(true);
                } else {
                    groupLabel(false);
                    $('#group_label').find('input').prop('checked', false);
                }
                if(select.val() == 1){
                    supplierInfo(true);
                } else {
                    supplierInfo(false);
                }
            }).trigger('change');

            var radio = $('input[name="supplier_info[shipping_costs]"]');
            radio.on('change', function () {
                radio.each(function () {
                    if ($(this).val() == 'shipping_less' && $(this).prop('checked') == true) {
                        $('#shippingLessThan').fadeIn();
                        $('#shippingLessThan').attr('required', true);
                        return false;
                    } else {
                        $('#shippingLessThan').fadeOut();
                        $('#shippingLessThan').attr('required', false);
                    }
                });
            }).trigger('change');

            $('#timepicker').timepicker({
                minuteStep: 1,
                showMeridian: false
            });
        });
        function groupLabel(state){
            var group = $('#group_label');
            if (state == true) {
                group.fadeIn();
            } else {
                group.fadeOut();
            }
        }
        function supplierInfo(state){
            var group = $('#supplier_info');
            if (state == true) {
                group.fadeIn();
                $('input[name="supplier_info[type]"]').attr('required', true);
                $('input[name="supplier_info[delivery_time]"]').attr('required', true);
                $('input[name="supplier_info[shipping_costs]"]').attr('required', true);
                $('input[name="supplier_info[delivery_workdays]"]').attr('required', true);
            } else {
                group.fadeOut();
                $('input[name="supplier_info[type]"]').attr('required', false);
                $('input[name="supplier_info[delivery_time]"]').attr('required', false);
                $('input[name="supplier_info[shipping_costs]"]').attr('required', false);
                $('input[name="supplier_info[delivery_workdays]"]').attr('required', false);
            }
        }
    </script>
{% endblock %}