{% extends "layouts/main.volt" %}
{% block title %} {{ "Tariff codes"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/tariff/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Tariff code"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <div style="width: 20% !important; z-index: 1; margin-top: 20px; margin-left: 470px; position: absolute;">
                <select id="product_category" name="product_category" class="select2-margin">
                    <option></option>
                    {% for pc in productCategories %}
                        <option value="{{ pc['id'] }}">{% if pc['cat_parent_name'] %}{{ pc['cat_parent_name'] }} - {% endif %}{% if pc['sub_parent_name'] %}{{ pc['sub_parent_name'] }} - {% endif %}{{ pc['name'] }}</option>
                    {% endfor %}
                </select>
            </div>
            <div style="width: 20% !important; z-index: 1; margin-top: 21px; margin-left: 820px; position: absolute;">
                <button class="btn btn-info btn-sm add-margin-all" disabled="disabled"><i class="pe-7s-plus"></i> {{ 'Add margin settings'|t }}</button>
            </div>

            <table id="code" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "Code"|t }}</th>
                    <th>{{ "Product name"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th>{{ "Price"|t }}</th>
                    <th>{{ "Margin settings"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for code in codes %}
                    <tr>
                        <td>{{ code.getCode() }}</td>
                        <td>{% if code.Product %}{{ code.Product.getName() }}{% endif %}</td>
                        {% if code.getCode() < 9000 %}
                        <td>{{ code.getDescription() }}</td>
                        {% else %}
                        <td>&nbsp;</td>
                        {% endif %}
                        <td>{% if code.Product %}{{ code.Product.getPrice() }}{% else %}{{ code.getPrice() }}{% endif %}</td>
                        <td>
                            {% if code.margin_type is 1 %}
                                {{ 'Fixed price'|t }}: {{ code.margin_value }} euro
                            {% elseif code.margin_type is 2 %}
                                {{ 'Fixed margin in euro on top of purchase price'|t }}: {{ code.margin_value }} euro
                            {% elseif code.margin_type is 3 %}
                                {{ 'As percentages of the purchase price'|t }}: {{ code.margin_value }}%
                            {% elseif code.margin_type is 4 %}
                                {{ 'As percentages of the sales price'|t }}: {{ code.margin_value }}%
                            {% endif %}
                        </td>
                        <td>
                            <a href="/signadens/tariff/edit/{{ code.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                            {% if code.margin_type and code.margin_value %}
                            <a class="btn btn-primary btn-sm edit-margin" data-id="{{ code.getId() }}" data-margintype="{{ code.getMarginType() }}" data-marginvalue="{{ code.getMarginValue() }}" data-roundingtype="{{ code.getRoundingType() }}"><i class="pe-7s-pen"></i> {{ 'Edit margin settings'|t }}</a>
                            {% else %}
                            <a class="btn btn-info btn-sm add-margin" data-id="{{ code.getId() }}"><i class="pe-7s-plus"></i> {{ 'Add margin settings'|t }}</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/addMarginSettings", ['id': 'add-modal', 'title': "Margin settings"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(function() {
            $('.select2-margin').select2({
                theme: "bootstrap",
                placeholder: '{{ "Select product category"|t }}'
            });

            $('.select2-margin').on('change', function(){
                $('.add-margin-all').removeAttr('disabled');
                $('#confirmButton').attr('data-procat' , $(this).val());
            });

            $('#margin_type').on('change', function(){

                if($(this).val() == 1 || $(this).val() == 2){

                    $('#margin_symbol').show();
                    $('#margin_symbol').html('euro');
                }
                else if($(this).val() != null) {

                    $('#margin_symbol').show();
                    $('#margin_symbol').html('%');
                }
            });

            // $('#rounding_type').on('change', function(){
            //
            //     if($(this).val() == 1){
            //
            //
            //         $('#margin_value').attr('step','0.01');
            //     }
            //
            //     if($(this).val() == 2){
            //
            //         if($('#margin_value').val() != null && $('#margin_value').val().length > 2){
            //
            //             $('#margin_value').val($('#margin_value').val().slice(0, -3));
            //         }
            //         $('#margin_value').removeAttr('step');
            //     }
            // });

            $('.add-margin').on('click', function () {

                $('#confirmButton').removeAttr('data-procat');
                $('#confirmButton').attr({'data-id' : $(this).attr('data-id'), 'data-type' : 'one'});
                $('#margin_type').val('').change();
                $('#margin_value').val(null);
                $('#rounding_type').val('').change();
                $('#add-modal').modal('show');
            });

            $('.add-margin-all').on('click', function () {

                $('#confirmButton').removeAttr('data-id');
                $('#confirmButton').attr({'data-type' : 'all', 'data-procat': $('.select2-margin').val()});
                $('#margin_type').val('').change();
                $('#margin_value').val(null);
                $('#rounding_type').val('').change();
                $('#add-modal').modal('show');
            });

            $('.edit-margin').on('click', function () {

                $('#confirmButton').attr('data-id', $(this).attr('data-id'));
                $('#margin_type').val($(this).attr('data-margintype')).change();
                $('#margin_value').val($(this).attr('data-marginvalue'));
                $('#rounding_type').val($(this).attr('data-roundingtype')).change();
                $('#add-modal').modal('show');
            });

            $('#confirmButton').on('click', function(){

                var req_type = $(this).attr('data-type');

                if(req_type == 'all'){
                    var id = $(this).attr('data-procat');
                }
                else {
                    var id = $(this).attr('data-id');
                }

                var margin_type = $('#margin_type').val();
                var margin_value = $('#margin_value').val();
                var rounding_type = $('#rounding_type').val();

                if(margin_type != '' && margin_type != null){
                    $('#margin_type').css("border-color", "transparent");
                }

                if(margin_value != '' && margin_value != null){
                    $('#margin_value').css("border-color", "transparent");
                }

                if(rounding_type != '' && rounding_type != null){
                    $('#rounding_type').css("border-color", "transparent");
                }

                if((margin_type != '' && margin_type != null) && (margin_value != '' && margin_value != null) && (rounding_type != '' && rounding_type != null)){

                    $.ajax({
                        method: 'POST',
                        url: '/signadens/tariff/ajaxmarginsettings/',
                        data: {
                            id: id, margin_type: margin_type, margin_value: margin_value, rounding_type: rounding_type, req_type: req_type
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status != "error"){
                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/tariff/';
                                    }, 1000);
                                }, 1000);
                            }
                            else {
                                setTimeout(function () {
                                    toastr.error(obj.msg);
                                }, 1000);
                            }
                        }
                    });
                    $('#margin_symbol').hide();
                    $('#add-modal').modal('hide');
                }
                else {
                    if(margin_type == '' || margin_type == null){
                        $('#margin_type').css("border-color", "red");
                    }
                    if(margin_value == '' || margin_value == null){
                        $('#margin_value').css("border-color", "red");
                    }
                    if(rounding_type == '' || rounding_type == null){
                        $('#rounding_type').css("border-color", "red");
                    }
                    toastr.error("{{ "Please fill in missing fields."|t }}");
                }
            });
        });
    </script>
{% endblock %}