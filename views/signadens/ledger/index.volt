{% extends "layouts/main.volt" %}
{% block title %} {{ "Ledger codes"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/ledger/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Ledger codes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="code" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "Code"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    {#<th>{{ "Group type"|t }}</th>
                    <th>{{ "Balance type"|t }}</th>
                    <th>{{ "Balance side"|t }}</th>
                    <th>{{ "Related Products"|t }}</th>
                    #}
                    <th class="sortbydate">{{ "Added on"|t }}</th>
                    <th>{{ "Added by"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </tr>
                </thead>
                <tbody>
                {% for code in codes %}
                    <tr>
                        <td>{{ code.getCode() }}</td>
                        <td>{{ code.getDescription() }}</td>
                        {#<td>{{ code.getGroupType() }}</td>
                        <td>{{ code.getBalanceType() }}</td>
                        <td>{{ code.getBalanceSide() }}</td>
                        <td>{% if code.Product != null %}<a href = "/signadens/product/edit/{{ code.Product.getId() }}">{{ code.Product.getName() }}</a>{% else %}-{% endif %}</td>
                        #}
                        <td><div class="hidden">{{ code.getCreatedAt() }}</div>{{ code.getCreatedAt()|dttonl }}</td>
                        <td>{{ code.CreatedBy.getFullName() }}</td>
                        <td><a href = "/signadens/ledger/edit/{{ code.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{'Edit'|t}}</a>
                            {% if code.getActive() %}
                                <a href="/signadens/ledger/deactivate/{{ code.getId() }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{'Deactivate'|t}}</a>
                            {% else %}
                                <a href="/signadens/ledger/activate/{{ code.getId() }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{'Activate'|t}}</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
{% endblock %}