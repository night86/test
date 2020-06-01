{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit organisation"|t }} {{ organisation.getName() }} {% endblock %}
{% block content %}

    <h3>{{ "Edit organisation"|t }}</h3>

    {{ form('signadens/organisation/edit/' ~ organisation.getId(), 'method': 'post', 'enctype': 'multipart/form-data') }}


    <fieldset class="form-group">

        <legend><a href="{{ url("signadens/organisation/") }}"><i
                        class="pe-7s-back"></i></a> {{ organisation.getName() }}</legend>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Name"|t }}:</label>
                    {{ text_field('organisation[name]', 'required': 'required', 'class': 'form-control', 'value': organisation.getName() ) }}
                </div>
                <div class="form-group">
                    <label>{{ "Street and number"|t }}:</label>
                    {{ text_field('organisation[address]', 'required': 'required', 'class': 'form-control', 'value': organisation.getAddress() ) }}
                </div>
                <div class="form-group">
                    <label>{{ "Zipcode"|t }}:</label>
                    {{ text_field('organisation[zipcode]', 'required': 'required', 'class': 'form-control', 'value': organisation.getZipcode() ) }}
                </div>
                <div class="form-group">
                    <label for="name">{{ 'Image'|t }}</label>
                    <input type="file" name="logo" class="form-control"
                           {% if organisation.logo is null %}required{% endif %}>
                </div>
                {% if organisation.logo is not null %}
                    <div class="form-group">
                        <a href="{{ url("signadens/organisation/deleteimageedit/")~organisation.getId() }}" class="btn btn-danger"><i class="pe-7s-trash"></i> {{ "Delete image"|t }}</a>
                    </div>
                {% endif %}
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "City"|t }}:</label>
                    {{ text_field('organisation[city]', 'required': 'required', 'class': 'form-control', 'value': organisation.getCity() ) }}
                </div>
                <div class="form-group">
                    <label>{{ "Country"|t }}:</label>
                    {{ select('organisation[country_id]', countries, 'required': 'required', 'class': 'form-control', 'value': organisation.getCountryId()) }}
                </div>
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('organisation[telephone]', 'required': 'required', 'class': 'form-control', 'value': organisation.getTelephone() ) }}
                </div>
                {% if organisation.logo is not null %}
                    <div class="form-group">
                        <img src="{{ image('organisation', organisation.logo) }}" width="300"/>
                    </div>
                {% endif %}
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "General emailaddress"|t }}:</label>
                    {{ text_field('organisation[email]', 'required': 'required', 'class': 'form-control', 'value': organisation.getEmail() ) }}
                </div>
                <div class="form-group">
                    <label>{{ 'Organisation type'|t }}:
                        <div id="group_label">
                            <label for="is_group">{{ 'Dentist group'|t }}:</label>
                            <input type="checkbox" name="organisation[is_group]" id="is_group" value="1" {% if organisation.getIsGroup() != null %}checked{% endif %} >
                        </div>
                    </label>
                    {#{{ select('organisation[organisation_type_id]', organisationTypes, 'required': 'required', 'class': 'form-control', 'value': organisation.getOrganisationTypeId() ) }}#}
                    <select id="select_organisation" name="organisation[organisation_type_id]" class="form-control" required="required">
                        {% for index, organisationType in organisationTypes %}
                            <option value="{{ index }}"
                                    {% if index is organisation.getOrganisationTypeId() %}selected{% endif %}>{{ organisationType|t }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ 'Active'|t }}:</label>
                    {#{{ select('organisation[active]', active, 'required': 'required', 'class': 'form-control', 'value': organisation.getActive() ) }}#}
                    <select name="organisation[active]" class="form-control" required="required">
                        {% for value, name in active %}
                            <option value="{{ value }}"
                                    {% if value is organisation.getActive() %}selected{% endif %}>{{ name|t }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>

            {# Supplier info #}
            <div class="col-md-4" id="supplier_info">
                <div class="form-group">
                    <h4>{{ "Type of supplier"|t }}</h4>
                    <div class="radio">
                        <label><input type="radio" name="supplier_info[type]" class="radio-inline" value="LOWEST_PRICE" {% if supplierInfo.getType() === 'LOWEST_PRICE' %}checked{% endif %}> {{ "Lowest price"|t }}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="supplier_info[type]" class="radio-inline" value="DISCOUNT_RATE" {% if supplierInfo.getType() === 'DISCOUNT_RATE' %}checked{% endif %}> {{ "Discount rate in framework agreement"|t }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <h4>{{ "Shipping costs"|t }}</h4>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" value="free_shipping" name="supplier_info[shipping_costs]" {% if supplierInfo.getShippingCosts() === 'free_shipping' %}checked{% endif %}> {{ "Free shipping"|t }}</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" id="shippingLess" value="shipping_less" name="supplier_info[shipping_costs]" {% if supplierInfo.getShippingCosts() === 'shipping_less' %}checked{% endif %}> {{ "Shipping costs will be charged when order is < €"|t }}
                        {#<div class="form-group">#}
                            <input type="number" name="supplier_info[shipping_less_than]" id="shippingLessThan" class="form-control numeric" value="{{ supplierInfo.getShippingLessThan() }}">
                        {#</div>#}
                        </label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" class="radio-inline" value="shipping_costs" name="supplier_info[shipping_costs]" {% if supplierInfo.getShippingCosts() === 'shipping_costs' %}checked{% endif %}> {{ "Additional shipping costs will be charged"|t }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <h4>{{ "Delivery time"|t }}</h4>
                    <div class="form-group">
                        <label>{{ "Delivery time in workdays"|t }}:</label>
                        <input type="text" name="supplier_info[delivery_workdays]" class="form-control" {% if supplierInfo !== false %} value="{{ supplierInfo.getDeliveryWorkdays() }}" {% endif %}>
                    </div>
                    <div class="form-group">
                        <label>{{ "When ordered before"|t }}:</label>
                        <div class="input-group bootstrap-timepicker timepicker">
                            <input id="timepicker" name="supplier_info[delivery_time]" type="text" class="form-control input-small" {% if supplierInfo !== false %} value="{{ supplierInfo.getDeliveryTime() }}" {% endif %}>
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                {#{{ submit_button('Save', 'class': 'btn btn-primary pull-right') }}#}
                <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}
                </button>
            </div>

        </div>
    </fieldset>

    {{ end_form() }}

    <form>
        <fieldset class="form-group">
            <legend>{{ "Access log"|t }}</legend>
            <table class="simple-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Date"|t }}</th>
                <th>{{ "Time"|t }}</th>
                <th>{{ "User"|t }}</th>
                <th>{{ "State"|t }}</th>
                </thead>
                <tbody>
                {% for log in logs %}
                    <tr>
                        <td>{% if log.datetime is defined %}{{ timetostrdt(log.datetime) }}{% else %}-{% endif %}</td>
                        <td>{% if log.datetime is defined %}{{ datetimetotime(log.datetime) }}{% else %}-{% endif %}</td>
                        <td>{{ log.username }} ({{ log.email }})</td>
                        <td>{{ log.state|t }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </fieldset>
    </form>

    <legend>{{ "Users"|t }}</legend>
    <table class="simple-datatable table table-striped">
        <thead>
        <th>{{ 'Active'|t }}</th>
        <th>{{ 'Email'|t }}</th>
        <th>{{ 'First name'|t }}</th>
        <th>{{ 'Last name'|t }}</th>
        <th>{{ 'Organisation'|t }}</th>
        <th>{{ 'Role'|t }}</th>
        <th>{{ 'Actions'|t }}</th>
        </thead>
        <tbody>
        {% for user in users %}
            <tr>
                <td>{{ active[user.getActive()] }}</td>
                <td>{{ user.getEmail() }}</td>
                <td>{{ user.getFirstName() }}</td>
                <td>{{ user.getLastName() }}</td>
                <td>{{ user.organisation.getName() }}</td>
                <td>{{ user.roleTemplate.name }}</td>
                <td>
                    <a href="{{ url("supplier/user/edit/" ~ user.getId()) }}" class="btn btn-primary btn-sm"><i
                                class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                    {% if user.getActive() == 1 %}
                        <a href="{{ url("supplier/user/deactivate/" ~ user.getId()) }}" class="btn btn-danger btn-sm"><i
                                    class="pe-7s-close-circle"></i> {{ 'Deactivate'|t }}</a>
                    {% else %}
                        <a href="{{ url("supplier/user/activate/" ~ user.getId()) }}" class="btn btn-success btn-sm"><i
                                    class="pe-7s-gleam"></i> {{ 'Activate'|t }}</a>
                    {% endif %}
                    <a href="{{ url("supplier/user/loginasuser/" ~ user.getId()) }}" class="btn btn-warning btn-sm"><i
                                class="pe-7s-glasses"></i> {{ 'Activate'|t }}</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

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