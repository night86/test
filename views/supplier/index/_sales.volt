<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingNewOrderLog">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" href="#collapseNewOrderLog"
                   aria-expanded="true" aria-controls="collapseNewOrderLog">
                    {% if orders is not empty and orders|length > 0 %}
                        <span class="badge">{{ orders|length }}</span>
                    {% endif %}
                    {{ "New order"|t }}
                </a>
            </h4>
        </div>
        <div id="collapseNewOrderLog" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="headingNewOrderLog">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {% if orders is not empty %}
                            <table class="table table-striped table-bordered" cellspacing="0"
                                   width="100%">
                                <thead>
                                    <th>{{ "Order number"|t }}</th>
                                    <th>{{ "Client"|t }}</th>
                                    <th class="sortbydate">{{ "Order date"|t }}</th>
                                </thead>
                                <tbody>
                                {% for k,order in orders if k < 5 %}
                                    <tr>
                                        <td><a href="{{ url('supplier/order/edit/' ~ order.getId()) }}">{{ order.getName() }}</a></td>
                                        <td>{% if order.Client %}{{ order.Client.getFullNameWithEmail() }}{% endif %}</td>
                                        <td><div class="hidden">{{ order.getCreatedAt() }}</div>{{ order.getCreatedAt()|dttonl }}</td>
                                    </tr>
                                {% endfor %}
                                </tbody>
                            </table>
                            <a href="{{ url('supplier/order/') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>