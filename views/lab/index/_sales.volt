<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingNewOrder">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" href="#collapseNewOrder"
                   aria-expanded="true" aria-controls="collapseNewProduct">
                    {% if neworders is not empty and neworders|length > 0 %}
                        <span class="badge">{{ neworders|length }}</span>
                    {% endif %}
                    {{ "New orders"|t }}
                </a>
            </h4>
        </div>
        <div id="collapseNewOrder" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="headingNewOrder">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {% if neworders is not empty %}
                            <table class="table table-striped table-bordered" cellspacing="0"
                                   width="100%">
                                <thead>
                                    <th>{{ "Order no."|t }}</th>
                                    <th>{{ "Dentist(client)"|t }}</th>
                                    <th>{{ "Delivery date"|t }}</th>
                                    <th>{{ "Amount"|t }}</th>
                                    <th>{{ "Actions"|t }}</th>
                                </thead>
                                <tbody>
                                {% for order in neworders %}
                                    {% if loop.index <= 5 %}
                                    <tr>
                                        <td>{{ order.code }}</td>
                                        <td>{{ order.CreatedBy.firstname }} {{ order.CreatedBy.lastname }}</td>
                                        <td>{% if order.delivery_at is defined %}{{ order.delivery_at }}{% endif %}</td>
                                        <td>{{ order.getTotal() }}</td>
                                        <td>
                                            <a href="{{ url('lab/sales_order/view/' ~ order.code) }}" class="btn btn-default btn-sm"><i class="pe-7s-look"></i> {{ 'Show'|t }}</a>
                                        </td>
                                    </tr>
                                    {% endif %}

                                {% endfor %}
                                </tbody>
                            </table>
                            <a href="{{ url('lab/sales_order/incoming') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>