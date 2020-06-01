{% extends "layouts/main.volt" %}
{% block title %} {{ "Connecting product categories to ledger codes"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Connecting product categories to ledger codes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="ledger_dt" class="buttons-ledger-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Product category"|t }}</th>
                    <th>{{ "Ledger code purchase"|t }}</th>
                    <th>{{ "Ledger code sales"|t }}</th>
                </thead>
                <tbody>
                {% for productCategory in productCategories %}
                    <tr>
                        <td>{% if productCategory.Parent and productCategory.Parent.Parent %}{{ productCategory.Parent.Parent.name }} - {% endif %}{% if productCategory.Parent %}{{ productCategory.Parent.name }} - {% endif %}{{ productCategory.name }}</td>
                        <td>
                            {% if productCategory.ledger_purchase_id is not null %}
                                <div class="pull-left">{{ productCategory.LedgerPurchase.code }} {{ productCategory.LedgerPurchase.description }}</div>
                                <div class="pull-right"><a class="btn btn-primary btn-sm edit" data-type="purchase" data-category="{{ productCategory.id }}" data-ledger="{{ productCategory.LedgerPurchase.id }}"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a></div>
                            {% else %}
                                <div class="pull-left"></div>
                                <div class="pull-right"><a class="btn btn-info btn-sm add" data-type="purchase" data-category="{{ productCategory.id }}"><i class="pe-7s-plus"></i> {{ 'Add'|t }}</a></div>
                            {% endif %}
                        </td>
                        <td>
                            {% if productCategory.ledger_sales_id is not null %}
                                <div class="pull-left">{{ productCategory.LedgerSales.code }} {{ productCategory.LedgerSales.description }}</div>
                                <div class="pull-right"><a class="btn btn-primary btn-sm edit" data-type="sales" data-category="{{ productCategory.id }}" data-ledger="{{ productCategory.LedgerSales.id }}"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a></div>
                            {% else %}
                                <div class="pull-left"></div>
                                <div class="pull-right"><a class="btn btn-info btn-sm add" data-type="sales" data-category="{{ productCategory.id }}"><i class="pe-7s-plus"></i> {{ 'Add'|t }}</a></div>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/mapLedgersCategories", ['id': 'confirm-modal', 'title': "Choose the ledger you want to connect"|t, 'ledgers': ledgerCodes, 'confirmButton': 'Save'|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            $('.add').on('click', function(){
               $('#confirm-modal').modal('show');
               $('.confirm-button').attr({"data-category": $(this).attr('data-category'), "data-type": $(this).attr('data-type'), "data-old-ledger": null});
               $('.confirm-button').prop('disabled', true);
            });

            $('.edit').on('click', function(){
                $('#confirm-modal').modal('show');
                $('.confirm-button').attr({"data-category": $(this).attr('data-category'), "data-type": $(this).attr('data-type'), "data-old-ledger": $(this).attr('data-ledger')});
                $('.confirm-button').prop('disabled', true);
                $('#ledger_select').val($(this).attr('data-ledger')).change();
            });

            $('#ledger_select').on('change', function(){
                $('.confirm-button').attr("data-new-ledger", $(this).val());
                $('.confirm-button').prop('disabled', false);
            });

            $('.confirm-button').on('click', function(){

                $.ajax({
                    method: "post",
                    url: "/signadens/manage/saveledgertocategories",
                    data: {
                        category: $(this).attr('data-category'),
                        old_ledger: $(this).attr('data-old-ledger'),
                        new_ledger: $(this).attr('data-new-ledger'),
                        type: $(this).attr('data-type')
                    },
                    dataType: 'json'
                }).success(function (data) {
                    if(data.status != "error"){
                        setTimeout(function () {
                            toastr.success(data.msg);
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        }, 1000);
                    }
                    else {
                        setTimeout(function () {
                            toastr.error(data.msg);
                        }, 1000);
                    }
                });
            });
        });
    </script>
{% endblock %}