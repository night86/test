{% extends "layouts/main.volt" %}
{% block title %} {{ "Orderlist"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Order history"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="history" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Order no."|t }}</th>
                    <th>{{ "Total"|t }}</th>
                    <th>{{ "Suppliers"|t }}</th>
                    <th>{{ "Order by"|t }}</th>
                    <th>{{ "Department"|t }}</th>
                    <th>{{ "Order date"|t }}</th>
                    <th>{{ "Status"|t }}</th>
                    <th>{{ "Details"|t }}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script type="text/javascript" src="/js/app/orderHistory.js"></script>
    <script>
        $(function(){
            order.init("{{ url('/lab/order/ajaxorderlist/1') }}");
            order.initDataTables();
        });
    </script>
{% endblock %}