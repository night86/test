{% extends "layouts/main.volt" %}
{% block title %} {{ "Cart"|t }} {% endblock %}
{% block content %}

    <div class="row">
        <div class="col-md-4" style="margin-bottom: 15px;">
            <h3>{{ 'Your cart'|t }}</h3>
            <select name="supplier" id="supplier-list" class="form-control">
                <option value="0">{{ "Select supplier"|t }}</option>
                {% for supplier in suppliers %}
                    <option value="{{ supplier }}">{{ supplier }}</option>
                {% endfor %}
            </select>
        </div>
        <div id="orglogo" class="col-md-8 text-right padding-15">
            {% for name, logo in suppliersLogo %}
                <img src="{{ logo }}" class="img-responsive pull-right" data-supplier="{{ name }}" alt="{{ name }} logo" style="max-width: 200px;"/>
            {% endfor %}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="cart" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Product name"|t }}</th>
                        <th>{{ "Delivery time in days"|t }}</th>
                        <th>{{ "Supplier"|t }}</th>
                        <th>{{ "ordered by"|t }}</th>
                        <th>{{ "Department"|t }}</th>
                        <th>{{ "Project No"|t }}</th>
                        <th>{{ "Product code"|t }}</th>
                        <th>{{ "Product price"|t }}</th>
                        <th>{{ "actions"|t }}</th>
                    </tr>
                </thead>
                <tfoot>
                    <th colspan="7" id="supplierText"></th>
                    <th>{{ "Total Ex. VAT"|t }}</th>
                    <th><span id="cart-price"></span></th>
                    <th></th>
                </tfoot>
            </table>
            {% if currentUser.hasRole('ROLE_LAB_CART_COMPLETEORDER') is true %}
                <a href="{{ url('/lab/cart/completeorder/'~orderName) }}" class="btn btn-success pull-right process" data-process="{{ url('/lab/cart/completeorder/') }}" data-productsurl="{{ url('/lab/product/') }}">{{ "Complete order"|t }} ></a>
            {% endif %}
            <a href="{{ url('/lab/cart/saveorder/'~orderName) }}" class="btn btn-primary pull-right saveprocess" data-process="{{ url('/lab/cart/saveorder/') }}" data-productsurl="{{ url('/lab/product/') }}"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</a>
        </div>
    </div>

    {{ partial("modals/alert", ['id': 'supplier-info', 'title': 'Warning', 'content': 'Please select supplier.']) }}
    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Confirmation"|t, 'content': "Are you sure you want to remove this product from the orderlist?"|t, 'confirmButton': 'Yes, I am sure'|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/cart.js?v=22"></script>
    <script>
        $(function(){
            cart.init("{{ url('/lab/cart/ajaxproductlist') }}");
            cart.initDataTables();

            var select = $('#supplier-list');
            select.on('change', function () {
                var infoArr = {{ suppliersTexts }},
                    info = infoArr[$.trim(select.val())];

                $('#supplierText').html('').html(info);
            }).trigger('change');
        });

    </script>
{% endblock %}