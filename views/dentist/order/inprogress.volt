{% extends "layouts/main.volt" %}
{% block title %} {{ "Orderlist"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("dentist/order/create") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "New order"|t }}</a></p>
    <h3>{{ "Dentist_orders"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <form id="filter_form" method="post" action="">
                <ul style="margin: 50px 0 0 250px; position: absolute; z-index: 1;">
                    {% if count(locations) > 1 %}
                        <li style="list-style: none; display: inline-block;">{{ "Search orders by location"|t }}</li>
                    {% endif %}
                    {% for loc in locations %}
                        <li style="list-style: none; display: inline-block; margin-left: 50px;"><input class="location-box" type="checkbox" name="location[{{ loc.id }}]" {% if (filters is not null and in_array(loc.id, filters))  %}value="1" checked="checked"{% else %}value="0"{% endif %} />&nbsp;{{ loc.name }}</li>
                    {% endfor %}
                    <li style="list-style: none; display: inline-block; margin-left: 50px;">
                        {#<button class="btn btn-primary">{{ "Show orders"|t }}</button>#}
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="history" class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <th class="sortbydate">{{ "Order date"|t }}</th>
                <th>{{ "Order no."|t }}</th>
                <th>{{ "Patient number"|t }}</th>
                <th>{{ "Patient name"|t }}</th>
                <th>{{ "BSN"|t }}</th>
                <th>{{ "Date of birth"|t }}</th>
                <th>{{ "Total"|t }}</th>
                <th>{{ "Dentist"|t }}</th>
                <th>{{ "Location"|t }}</th>
                <th>{{ "Status"|t }}</th>
                <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% for order in orders %}
                    <tr>
                        <td><div class="hidden">{{ order.order_at }}</div>{{ order.order_at|dttonl }}</td>
                        <td>{{ order.code }}</td>
                        <td>{{ order.DentistOrderData.getPatientNumber() }}</td>
                        <td>{{ order.DentistOrderData.getPatientInitials() }} {{ order.DentistOrderData.getPatientInsertion() }} {{ order.DentistOrderData.getPatientLastname() }}</td>
                        <td>{{ order.DentistOrderBsn.getBsnSecured() }}</td>
                        <td><div class="hidden">{{ order.DentistOrderData.getPatientBirth() }}</div>{% if order.DentistOrderData.getPatientBirth() is not null %}{{ order.DentistOrderData.getPatientBirth()|dttonl }}{% endif %}</td>
                        <td>{{ order.getTotal() }}</td>
                        <td>{% if order.DentistUser %}{{ order.DentistUser.firstname }} {{ order.DentistUser.lastname }}{% endif %}</td>
                        <td>{% if count(locations) > 1 and order.DentistLocation %}{{ order.DentistLocation.getName() }}{% else %}{{ "Main location"|t }}{% endif %}</td>
                        <td>{{ order.getStatusLabel()|t }}</td>
                        <td>
                            <div style="min-width: 420px;">
                                <a href="{{ url('dentist/order/edit/' ~ order.code) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                                <a href="{{ url('dentist/order/delete/' ~ order.code) }}" class="delete-order btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{'Delete'|t}}</a>

                                <a href="{{ url('dentist/order/packingpdf/' ~ order.code) }}" class="btn btn-default btn-sm" target="_blank" title="{{ 'Get PDF packing slip'|t }}"><i class="pe-7s-download"></i> {{ 'Packing slip'|t }}</a>
                                <a href="{{ url('dentist/order/packingpdfprint/' ~ order.code) }}" class="btn btn-default btn-sm" target="_blank" title="{{ 'Print packing slip'|t }}"><i class="pe-7s-print"></i> {{ 'Packing slip'|t }}</a>

                                <a href="{{ url('dentist/order/printlabel/' ~ order.code) }}" class="btn btn-default btn-sm" target="_blank" title="{{ 'Get PDF label'|t }}"><i class="pe-7s-download"></i> {{ 'Label'|t }}</a>
                                <a href="{{ url('dentist/order/printlabelprint/' ~ order.code) }}" class="btn btn-default btn-sm" target="_blank" title="{{ 'Print label'|t }}"><i class="pe-7s-print"></i> {{ 'Label'|t }}</a>
                            </div>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Delete"|t, 'content': "Are you sure you want to remove this order?"|t, 'confirmButton': 'Yes, I am sure'|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            $('.delete-order').on('click', function(e){
                e.preventDefault();
                $href = $(this).attr('href');
                var confirmModal = $('#confirm-modal');
                confirmModal.modal('show');
                console.log($href);

                $('.confirm-button').on('click', function(){
                    confirmModal.modal('hide');
                    window.location = $href;
                });
            });

            $('.location-box').on('change', function(){
               if($(this).is(':checked')){
                   $(this).val(1);
               }
               else {
                   $(this).val(0);
               }
                $('#filter_form').submit();
            });
        });
    </script>
{% endblock %}