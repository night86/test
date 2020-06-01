{% extends "layouts/main.volt" %}
{% block title %} {{ "Tariff code ranges"|t }} {% endblock %}
{% block content %}

    <h3>
        {{ "Tariff code ranges"|t }}
        <span class="pull-right"><a id="add_tcr" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Manufacturer"|t }}</th>
                    <th>{{ "Product category"|t }}</th>
                    <th>{{ "Range from"|t }}</th>
                    <th>{{ "Range to"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% if codeTariffRanges is not null %}
                    {% for ctr in codeTariffRanges %}
                        <tr>
                            <td>{{ ctr.Manufacturer.name }}</td>
                            <td>{% if ctr.ProductCategory.Parent and ctr.ProductCategory.Parent.Parent %}{{ ctr.ProductCategory.Parent.Parent.name }} - {% endif %}{% if ctr.ProductCategory.Parent %}{{ ctr.ProductCategory.Parent.name }} - {% endif %}{{ ctr.ProductCategory.name }}</td>
                            <td>{{ ctr.getRangeFrom() }}</td>
                            <td>{{ ctr.getRangeTo() }}</td>
                            <td>
                                <a data-id="{{ ctr.id }}" data-man="{{ ctr.manufacturer_id }}" data-procat="{{ ctr.product_category_id }}" data-from="{{ ctr.range_from }}" data-to="{{ ctr.range_to }}" class="edit-tcr btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                            </td>
                        </tr>
                    {% endfor %}
                {% endif %}
                </tbody>
            </table>
        </div>
    </div>
    {{ partial("modals/addTariffCodeRange", ['id': 'add-modal', 'title': "Add a tariff code range"|t]) }}
    {{ partial("modals/editTariffCodeRange", ['id': 'edit-modal', 'title': "Edit tariff code range"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(function(){
            $('#add_tcr').on('click', function(){
                //alert(code);
                $('#add-modal').modal('show');

                $('#confirmButton').on('click', function(){

                    var manufacturer_id = $('#manufacturer_id').val();
                    var product_category_id = $('#product_category').val();
                    var range_from = $('#range_from').val();
                    var range_to = $('#range_to').val();

                    if(manufacturer_id != '' && product_category != '' && range_from != '' && range_to != ''){

                        $.ajax({
                            method: 'POST',
                            url: '/signadens/manage/ajaxtariffcoderanges/',
                            data: {
                                type: 'new', manufacturer_id: manufacturer_id, product_category_id: product_category_id, range_from: range_from, range_to: range_to
                            },
                            success: function(data){

                                var obj = $.parseJSON(data);

                                if(obj.status != "error"){

                                    $('#man_error').hide();
                                    $('#ran_error').hide();

                                    setTimeout(function () {
                                        toastr.success(obj.msg);
                                        setTimeout(function () {
                                            location.href = '/signadens/manage/tariffcoderanges/';
                                        }, 1000);
                                    }, 1000);
                                }
                                else {
                                    if(obj.type == "man"){

                                        $('#man_error').show();
                                    }
                                    else if(obj.type == "ran"){

                                        $('#ran_error').show();
                                    }
                                    else {
                                        setTimeout(function () {
                                            toastr.error(obj.msg);
                                        }, 1000);
                                    }
                                }
                            }
                        });
                    }
                    else {
                        toastr.error("{{ "Please fill in missing fields."|t }}");
                    }
                });
            });

            $('.edit-tcr').on('click', function(){

                $('#edit-modal').modal('show');
                $('#confirmEditButton').attr('data-id', $(this).attr('data-id'));
                $('#manufacturer_id_edit').val($(this).attr('data-man')).change();
                $('#product_category_edit').val($(this).attr('data-procat')).change();
                $('#range_from_edit').val($(this).attr('data-from'));
                $('#range_to_edit').val($(this).attr('data-to'));

                $('#confirmEditButton').on('click', function(){

                    var id = $(this).attr('data-id');
                    var manufacturer_id = $('#manufacturer_id_edit').val();
                    var product_category_id = $('#product_category_edit').val();
                    var range_from = $('#range_from_edit').val();
                    var range_to = $('#range_to_edit').val();

                    $.ajax({
                        method: 'POST',
                        url: '/signadens/manage/ajaxtariffcoderanges/',
                        data: {
                            type: 'old', id: id, manufacturer_id: manufacturer_id, product_category_id: product_category_id, range_from: range_from, range_to: range_to
                        },
                        success: function(data){

                            var obj = $.parseJSON(data);

                            if(obj.status != "error"){

                                $('#man_error_edit').hide();
                                $('#ran_error_edit').hide();

                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/manage/tariffcoderanges/';
                                    }, 1000);
                                }, 1000);
                            }
                            else {
                                if(obj.type == "man"){

                                    $('#man_error_edit').show();
                                }
                                else if(obj.type == "ran"){

                                    $('#ran_error_edit').show();
                                }
                                else {
                                    setTimeout(function () {
                                        toastr.error(obj.msg);
                                    }, 1000);
                                }
                            }
                        }
                    });
                });
            });
        });
    </script>

{% endblock %}
