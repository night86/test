{% extends "layouts/main.volt" %}
{% block title %} {{'Cart'|t}} {% endblock %}
{% block content %}

    <div class="row">
        <div class="col-md-12">
            <h3>{{ "Order"|t }} {{ orderName }} {{ "on delivery"|t }}</h3>
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
                    </tr>
                </thead>
                <tfoot>
                    <th colspan="5"></th>
                    <th>{{ "Total Ex. VAT"|t }}</th>
                    <th><span id="cart-price"></span></th>
                </tfoot>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    {#<script type="text/javascript" src="/js/app/cart.js"></script>#}
    <script>
        {#$(function(){#}
            {#cart.init("{{ url('/supplier/order/ajaxbuyedproductlist/' ~ order.id) }}");#}
            {#cart.initDataTablesCompleteOrder();#}
        {#});#}
    </script>
{% endblock %}