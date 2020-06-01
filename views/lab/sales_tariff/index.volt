{% extends "layouts/main.volt" %}
{% block title %} {{ "Tariff codes"|t }} {% endblock %}
{% block content %}
    <p class="pull-right"><a href="{{ url("lab/sales_tariff/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Tariff codes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="code" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Code"|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Price"|t }}</th>
                        {#<th>{{ "Related Recipes"|t }}</th>#}
                        {#<th class="sortbydate">{{ "Added on"|t }}</th>#}
                        {#<th>{{ "Added by"|t }}</th>#}
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for code in codes %}
                        <tr>
                            <td>{{ code.getCode() }}</td>
                            <td>{{ code.getDescription() }}</td>
                            <td>&euro;{{ code.getPrice() }}</td>
                            {#<td>{% if code.Recipe != null %}<a href = "/lab/recipe/view/{{ code.Recipe.getId() }}">{{ code.Recipe.getName() }}</a>{% else %}-{% endif %}</td>#}
                            {#<td><div class="hidden">{{ code.getCreatedAt() }}</div>{{ code.getCreatedAt()|dttonl }}</td>#}
                            {#<td>{{ code.CreatedBy.getFullName() }}</td>#}
                            <td><a href = "/lab/sales_tariff/edit/{{ code.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{'Edit'|t}}</a>
                                {% if code.getActive() %}
                                    <a href="/lab/sales_tariff/deactivate/{{ code.getId() }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{'Deactivate'|t}}</a>
                                {% else %}
                                    <a href="/lab/sales_tariff/activate/{{ code.getId() }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{'Activate'|t}}</a>
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