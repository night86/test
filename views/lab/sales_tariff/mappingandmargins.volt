{% extends "layouts/main.volt" %}
{% block title %} {{ "Mapping and margins"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Mapping and margins"|t }}</h3>

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

            <table id="map" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "Tariff code Signadens"|t }}</th>
                    <th>{{ "Description / Product name"|t }}</th>
                    <th>{{ "Mapped to lab tariff code"|t }}</th>
                    <th>{{ "Margin settings Signadens"|t }}</th>
                    <th>{{ "Margin settings lab"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for tariff in signaTariffs %}
                    {% if tariff.organisation_id is signaId %}
                    <tr>
                        <td>{{ tariff.code }}</td>
                        <td>{% if tariff.code < 9000 %}{{ tariff.description }}{% else %}{% if tariff.Product %}{{ tariff.Product.name }}{% endif %}{% endif %}</td>
                        <td>{% if tariff.LabTariff %}{{ tariff.LabTariff.code }} - {{ tariff.LabTariff.description }} <a class="btn btn-danger remove-map-tariff" data-id="{{ tariff.id }}" style="float: right;"><i class="pe-7s-close-circle"></i> </a>{% endif %}</td>
                        <td>
                            {% if tariff.margin_type is 1 %}
                                {{ 'Fixed price'|t }}: {{ tariff.margin_value }} euro
                            {% elseif tariff.margin_type is 2 %}
                                {{ 'Fixed margin in euro on top of purchase price'|t }}: {{ tariff.margin_value }} euro
                            {% elseif tariff.margin_type is 3 %}
                                {{ 'As percentages of the purchase price'|t }}: {{ tariff.margin_value }}%
                            {% elseif tariff.margin_type is 4 %}
                                {{ 'As percentages of the sales price'|t }}: {{ tariff.margin_value }}%
                            {% endif %}
                        </td>
                        <td>
                            {% if tariff.margin_type_lab is 1 %}
                                {{ 'Fixed price'|t }}: {{ tariff.margin_value_lab }} euro
                            {% elseif tariff.margin_type_lab is 2 %}
                                {{ 'Fixed margin in euro on top of purchase price'|t }}: {{ tariff.margin_value_lab }} euro
                            {% elseif tariff.margin_type_lab is 3 %}
                                {{ 'As percentages of the purchase price'|t }}: {{ tariff.margin_value_lab }}%
                            {% elseif tariff.margin_type_lab is 4 %}
                                {{ 'As percentages of the sales price'|t }}: {{ tariff.margin_value_lab }}%
                            {% endif %}
                            {% if tariff.margin_type_lab is not null and tariff.margin_value_lab is not null %}
                                <a class="btn btn-danger remove-margin-tariff" data-id="{{ tariff.id }}" style="float: right;"><i class="pe-7s-close-circle"></i> </a>
                            {% endif %}
                        </td>
                        <td>
                            {% if not tariff.LabTariff and tariff.code < 9000 %}
                            <a class="btn btn-info map-tariff" data-id="{{ tariff.id }}" data-code="{{ tariff.code }}" data-desc="{{ tariff.description }}"><i class="pe-7s-plus"></i> {{ "Map tariff code"|t }}</a>
                            {% endif %}
                            <a class="btn btn-primary add-margin" data-id="{{ tariff.id }}" {% if tariff.margin_type_lab is not null and tariff.margin_value_lab is not null and tariff.rounding_type_lab is not null %}data-margin-type="{{ tariff.margin_type_lab }}" data-margin-value="{{ tariff.margin_value_lab }}" data-rounding-type="{{ tariff.rounding_type_lab }}"{% endif %}><i class="pe-7s-pen"></i> {{ "Edit margin"|t }}</a>
                        </td>
                    </tr>
                    {% endif %}
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>





    {{ partial("modals/addMarginSettings", ['id': 'add-modal', 'title': "Margin settings"|t]) }}
    {{ partial("modals/removeMappingSettings", ['id': 'remove_mapping_modal', 'additionalClass': "confirm-remove-tariff"]) }}
    {{ partial("modals/removeMappingSettings", ['id': 'remove_margin_modal', 'additionalClass': "confirm-remove-margin"]) }}
    {{ partial("modals/mapLabTariff") }}

{% endblock %}

{% block scripts %}
    {{ super() }}
<script>
    $(function(){

        $('.map-tariff').on('click', function(){

            $('.confirm-map-tariff').attr("data-signa", $(this).attr('data-id'));
            $('#signa_tariff').html($(this).attr('data-code')+' - '+$(this).attr('data-desc'));
            $('#map_lab_tariff').modal('show');
        });

        $('#lab_tariff_id').on('change', function(){

            $('.confirm-map-tariff').attr("data-lab", $(this).val());

            if($(this).val() != ''){
                $('.confirm-map-tariff').prop('disabled', false);
            }
            else {
                $('.confirm-map-tariff').prop('disabled', true);
            }

        });

        $('.confirm-map-tariff').on('click', function(){

            $.ajax({
                method: 'POST',
                url: '/lab/sales_tariff/ajaxmaptariff',
                data: { signa_tariff_id: $(this).attr('data-signa'), lab_tariff_id: $(this).attr('data-lab') },
                success: function(data){

                    var obj = $.parseJSON(data);

                    if (obj.status === 'ok') {
                        setTimeout(function () {
                            toastr.success(obj.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }, 1000);
                    }
                    else {
                        setTimeout(function () {
                            toastr.error(obj.msg)
                        }, 1000);
                    }
                },
            });
        });

        $('.remove-map-tariff').on('click', function(){

            $('.confirm-remove-tariff').attr('data-id', $(this).attr('data-id'));
            $('#remove_mapping_modal').modal('show');
        });

        $('.confirm-remove-tariff').on('click', function(){

            $.ajax({
                method: 'POST',
                url: '/lab/sales_tariff/ajaxremovetariff',
                data: { id: $(this).attr('data-id') },
                success: function(data){

                    var obj = $.parseJSON(data);

                    if (obj.status === 'ok') {
                        setTimeout(function () {
                            toastr.success(obj.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }, 1000);
                    }
                    else {
                        setTimeout(function () {
                            toastr.error(obj.msg)
                        }, 1000);
                    }
                },
            });
        });

        $('.select2-margin').select2({
            theme: "bootstrap",
            placeholder: '{{ "Select product category"|t }}'
        });

        $('.select2-margin').on('change', function(){
            $('.add-margin-all').removeAttr('disabled');
            $('#confirmButton').attr('data-procat' , $(this).val());
        });

        $('.add-margin').on('click', function () {

            if($(this).attr('data-margin-type')){

                $('#margin_type').val($(this).attr('data-margin-type')).change();
                $('#margin_symbol').show();

                if($(this).attr('data-margin-type') == 1 || $(this).attr('data-margin-type') == 2){

                    $('#margin_symbol').html('euro');
                }
                if($(this).attr('data-margin-type') == 3 || $(this).attr('data-margin-type') == 4){

                    $('#margin_symbol').html('%');
                }
            }
            else {
                $('#margin_symbol').hide();
                $('#margin_type').val('').change();
            }

            if($(this).attr('data-margin-value')){

                $('#margin_value').val($(this).attr('data-margin-value'));
            }
            else {
                $('#margin_value').val(null);
            }

            if($(this).attr('data-rounding-type')){

                $('#rounding_type').val($(this).attr('data-rounding-type')).change();
            }
            else {
                $('#rounding_type').val('').change();
            }

            $('#confirmButton').removeAttr('data-procat');
            $('#confirmButton').attr({'data-id' : $(this).attr('data-id'), 'data-type' : 'one'});
            $('#add-modal').modal('show');
        });

        $('.add-margin-all').on('click', function () {

            $('#margin_symbol').hide();
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

        $('.remove-margin-tariff').on('click', function(){

            $('.confirm-remove-margin').attr('data-id', $(this).attr('data-id'));
            $('#remove_margin_modal').modal('show');
        });

        $('.confirm-remove-margin').on('click', function(){

            $.ajax({
                method: 'POST',
                url: '/lab/sales_tariff/ajaxremovemargin',
                data: { id: $(this).attr('data-id') },
                success: function(data){

                    var obj = $.parseJSON(data);

                    if (obj.status === 'ok') {
                        setTimeout(function () {
                            toastr.success(obj.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }, 1000);
                    }
                    else {
                        setTimeout(function () {
                            toastr.error(obj.msg)
                        }, 1000);
                    }
                },
            });
        });

        $('#margin_type').on('change', function(){

            $('#margin_symbol').show();

            if($(this).val() == 1 || $(this).val() == 2){

                $('#margin_symbol').html('euro');
            }
            if($(this).val() == 3 || $(this).val() == 4){

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
                    url: '/lab/sales_tariff/ajaxmarginsettings/',
                    data: {
                        id: id, margin_type: margin_type, margin_value: margin_value, rounding_type: rounding_type, req_type: req_type
                    },
                    success: function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status != "error"){
                            setTimeout(function () {
                                toastr.success(obj.msg);
                                setTimeout(function () {
                                    location.href = '/lab/sales_tariff/mappingandmargins';
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