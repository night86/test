{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit order"|t }} {{ orderName }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("supplier/order/") }}"><i class="pe-7s-back"></i></a> {{ 'Order'|t }} {{ orderName }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="cart" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "Amount"|t }}</th>
                    <th>{{ "Product name"|t }}</th>
                    <th>{{ "Product material"|t }}</th>
                    <th>{{ "Supplier"|t }}</th>
                    <th>{{ "Project No"|t }}</th>
                    <th>{{ "Product code"|t }}</th>
                    <th>{{ "Product price"|t }}</th>
                    <th>{{ "Sent"|t }}</th>
                </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th>{{ "Total"|t }}</th>
                        <th><span id="cart-price"></span></th>
                    </tr>
                </tfoot>
            </table>
            <a href="{{ url('/supplier/order/completeorder/'~orderName) }}"
               class="btn btn-success pull-right process">{{ "Move to order history"|t }} ></a>
            <a href="{{ url('/supplier/order/sentpiece/'~orderName) }}" class="btn btn-primary pull-right"
               id="sendpiece">{{ "Save"|t }} <i class="pe-7s-diskette" aria-hidden="true"></i></a>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            var translations = {
                'sentErrorNotification': "{{ "Not all products in this order are sent yet"|t }}",
                'sentStatusUpdate': "{{ "Order has been updated."|t }}"
            };
            cart.showAllSupplier = false;
            cart.setTranslations(translations);
            cart.init("{{ url('/supplier/order/ajaxproductlist/' ~ order.id) }}");
            cart.initDataTablesCompleteSuplierOrderDetails();

            $('#sendpiece').on('click', function (e) {
                e.preventDefault();
            });
        });
    </script>
{% endblock %}