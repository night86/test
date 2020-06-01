{% extends "layouts/main.volt" %}
{% block title %} {{'Order'|t}} {% endblock %}
{% block content %}

    <div class="row">
        <div class="col-md-12">
            <h3><a href="{% if order.status < 4 %}{{ url("lab/order/") }}{% else %}{{ url("lab/order/history/") }}{% endif %}"><i class="pe-7s-back"></i></a> {{ "Your order"|t }} {{ orderName }} {{ "on"|t }} {{ orderDate }}</h3>
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
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tfoot>
                    <th colspan="5"></th>
                    <th>{{ "Total Ex. VAT"|t }}</th>
                    <th><span id="cart-price"></span></th>
                    <th><label class="received-label"><input type="checkbox" class="check_all" style="margin-right: 10px;">{{ "Received all"|t }}</label></th>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {% if order.status < 4 %}
                <a href="javascript:;" class="btn btn-primary pull-right movetohistory"><i class="pe-7s-next-2"></i> {{ "Move to order history"|t }}</a>
            {% endif %}
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/cart.js"></script>
    <script>
        $(function(){
            cart.init("{{ url('/lab/order/ajaxbuyedproductlist/'~orderName) }}");
            cart.initDataTablesCompleteOrderDetails()
            cart.receivedProduct('{{ orderName }}');
            cart.moveToHistory('{{ orderName }}');
            cart.receivedAll('{{ orderName }}');
        });
    </script>
{% endblock %}