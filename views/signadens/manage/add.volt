{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new framework agreement"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/manage/index") }}"><i class="pe-7s-back"></i></a> {{ "Add new framework agreement"|t }}</h3>

    {{ form('signadens/manage/add', 'method': 'post', 'enctype' : 'multipart/form-data') }}

    <fieldset class="form-group">
        <legend>{{ "Framework agreement"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Agreement effective from'|t}}:</label>
                    {{ text_field('agreement[start_date]', 'required': 'required', 'class': 'form-control datepicker') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Due date agreement'|t}}:</label>
                    {{ text_field('agreement[due_date]', 'required': 'required', 'class': 'form-control datepicker') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Owner of agreement'|t}}:</label>
                    {{ select('agreement[user_id]', owners, 'required': 'required', 'class': 'form-control') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Reminder due date in month'|t}}:</label>
                    {{ numeric_field('agreement[reminder]', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Name supplier'|t}}:</label>
                    {{ select('agreement[supplier_id]', suppliers, 'required': 'required', 'class': 'form-control', 'id': 'supplier_select') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Name contact person'|t}}:</label>
                    {{ text_field('agreement[contact_person]', 'class': 'form-control') }}
                </div>
            </div>
        </div>

        <div class="row supplier">
            <div class="col-md-4">
                <label>{{ 'Address supplier'|t }}</label>
                <input disabled class="form-control" type="text" id="address_supplier">
            </div>
            <div class="col-md-4">
                <label>{{ 'Zip code supplier'|t }}</label>
                <input disabled class="form-control" type="text" id="zip_supplier">
            </div>
            <div class="col-md-4">
                <label>{{ 'City supplier'|t }}</label>
                <input disabled class="form-control" type="text" id="city_supplier">
            </div>
        </div>
        <div class="row supplier">
            <div class="col-md-4">
                <label>{{ 'Phone number supplier'|t }}</label>
                <input disabled class="form-control" type="text" id="phone_supplier">
            </div>
            <div class="col-md-4">
                <label>{{ 'Shipping costs'|t }}</label>
                <input disabled class="form-control" type="text" id="shipping_supplier">
            </div>
            <div class="col-md-4">
                <label>{{ 'Delivery period'|t }}</label>
                <input disabled class="form-control" type="text" id="delivery_supplier">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label>{{ 'Suggested retail price'|t }}</label>
                {{ file_field('files[retail_price][]', 'class': 'form-control', 'multiple': 'multiple') }}
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ 'Order quantity'|t }}</label>
                    {{ text_field('agreement[order_quantity]', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <label>{{ 'Framework agreement'|t }}</label>
                {{ file_field('files[agreement][]', 'class': 'form-control', 'multiple': true) }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label>{{ 'Attachments'|t }}</label>
                {{ file_field('files[attachments][]', 'class': 'form-control', 'multiple': true) }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <h4>{{ 'Categories the agreement is applicable to'|t }}</h4>
                    <div><label><input type="radio" name="agreement[discount_type]" value="ALL" class="" checked> {{ 'Discount for all products'|t }} {{ text_field('discounts[all][discount]', 'id': 'discount_all_value', 'class': 'number form-control', 'required': 'required', 'value': 0) }}%</label></div>
                    <div>
                        <label><input type="radio" name="agreement[discount_type]" value="CATEGORY" class="discount_category"> {{ 'Discount per product category'|t }}</label>
                        <div class="discount_types discount_category_block">
                            {% for category in categories %}
                                <div class="radio">
                                    {{ hidden_field('discounts[category]['~loop.index~'][relative_id]', 'value': category.id) }}
                                    <label>{{ category.name|t }} {{ text_field('discounts[category]['~loop.index~'][discount]', 'class': 'number form-control') }}%</label>
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                    <div>
                        <label><input type="radio" name="agreement[discount_type]" value="GROUP" class="discount_group"> {{ 'Discount per product group'|t }}</label>
                        <div class="discount_types discount_group_block">
                            {% for group in recipes %}
                                <div class="radio">
                                    {{ hidden_field('discounts[group]['~loop.index~'][relative_id]', 'value': group.product_group) }}
                                    <label>{{ group.product_group|t }} {{ text_field('discounts[group]['~loop.index~'][discount]', 'class': 'number form-control') }}%</label>
                                </div>
                            {% endfor %}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h4>{{ 'Marketing support applicability'|t }}</h4>
                <label>{{ radio_field('agreement[support_applicability]', 'value': '0', 'checked': 'checked') }} No</label>
                <label>{{ radio_field('agreement[support_applicability]', 'value': '1') }} Yes</label>
                <div id="support_applicability_attachments">
                    {{ file_field('files[support_applicability][]', 'class': 'form-control', 'multiple': 'multiple') }}
                    {{ text_area('agreement[support_applicability_text]', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <h4>{{ 'SLA applicability'|t }}</h4>
                <label>{{ radio_field('agreement[sla_applicability]', 'value': '0', 'checked': 'checked') }} No</label>
                <label>{{ radio_field('agreement[sla_applicability]', 'value': '1', 'id': 'sla_applicability') }} Yes</label>
                <div id="sla_applicability_attachments" >
                    {{ file_field('files[sla_applicability][]', 'class': 'form-control', 'multiple': 'multiple') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label>{{ 'Available training sessions'|t }}</label>
                {{ text_area('agreement[available_training_sessions]', 'class': 'form-control') }}
            </div>
            <div class="col-md-6">
                <label>{{ 'Additional notes'|t }}</label>
                {{ text_area('agreement[notes]', 'class': 'form-control') }}
            </div>
        </div>

        <br><br>

        <legend>
            {{ 'Notifications'|t }}
            <span class="pull-right"><a href="javascript:;" class="add-new-row add-reminder"><i
                            class="pe-7s-plus"></i></a></span>
        </legend>
        <div class="row">
            <div class="col-md-12">
                <table id="reminders" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <th width="30%">{{ "Subject"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th width="10%">{{ "Send date"|t }}</th>
                    <th width="15%">{{ "Actions"|t }}</th>
                    </thead>
                    <tbody class="reminders-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
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

    <div class="reminder-pattern hidden">
        <table>
            <tr class="reminder-row">
                <td>{{ text_field('reminder[subject][]', 'class': 'form-control', 'required': true) }}</td>
                <td>{{ text_area('reminder[description][]', 'class': 'form-control') }}</td>
                <td>{{ text_field('reminder[send_at][]', 'class': 'form-control datepicker', 'required': true) }}</td>
                <td><a href="javascript:;" class="btn btn-danger btn-sm reminder-remove-row"><i
                                class="pe-7s-close-circle"></i></a></td>
            </tr>
        </table>
    </div>

{% endblock %}

{% block styles %}
    {{ super() }}
    <style>
        .number {
            display: inline-block;
            width: 45px;
        }
    </style>
{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {

            $('.discount_types').hide();
            $('input[name="agreement[discount_type]"]').change(function() {
                if ($(this).is(':checked')) {
                    var current = $('.' + $(this).prop('class') + "_block").show();
                    $('.discount_types').not(current).hide();
                    $('.discount_types input').not(current).removeAttr('required');
                    $('input', current).each(function(){ $(this).prop('required', 'required'); });
                    var discount = $('#discount_all_value');
                    if('ALL' == $(this).val()) discount.prop('required', 'required');
                    else discount.removeAttr('required');
                }
            });

            var select = $('#supplier_select');
            select.on('change', function () {
                $('#address_supplier').val('');
                $('#zip_supplier').val('');
                $('#city_supplier').val('');
                $('#phone_supplier').val('');
                $('#shipping_supplier').val('');
                $('#delivery_supplier').val('');

                $.ajax({
                    url: '/signadens/manage/ajaxsupplierinfo',
                    method: 'POST',
                    data: { 'id': select.val() },
                    success: function(data) {
                        data = $.parseJSON(data);
                        $('#address_supplier').val(data.address);
                        $('#zip_supplier').val(data.zip);
                        $('#city_supplier').val(data.city);
                        $('#phone_supplier').val(data.phone);
                        $('#shipping_supplier').val(data.shipping);
                        $('#delivery_supplier').val(data.delivery);
                    }
                });
            }).trigger('change');

            var radioSla = $('input[name="agreement[sla_applicability]"]');
            radioSla.on('change', function () {
                radioSla.each(function () {
                    if ($(this).val() == '1' && $(this).prop('checked') == true) {
                        $('#sla_applicability_attachments').fadeIn();
                        return false;
                    } else {
                        $('#sla_applicability_attachments').fadeOut();
                        $('#support_applicability_attachments input').val('');
                    }
                });
            }).trigger('change');

            var radioSA = $('input[name="agreement[support_applicability]"]');
            radioSA.on('change', function () {
                radioSA.each(function () {
                    if ($(this).val() == '1' && $(this).prop('checked') == true) {
                        $('#support_applicability_attachments').fadeIn();
                        return false;
                    } else {
                        $('#support_applicability_attachments').fadeOut();
                        $('#support_applicability_attachments input').val('');
                        $('#support_applicability_attachments textarea').val('');
                    }
                });
            }).trigger('change');

            $('.number').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

        });
    </script>
{% endblock %}
