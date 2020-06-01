{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3>
        {{ "Recipes"|t }}
        <span class="pull-right"><a href="{{ url("signadens/product/add") }}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table id="clients" class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                {#<th>{{ "Code"|t }}</th>#}
                <th>{{ "Recipe number"|t }}</th>
                <th>{{ "Name"|t }}</th>
                {#<th>{{ "Actions"|t }}</th>#}
                </thead>
                <tbody>
                {% for recipe in recipes %}
                    <tr>
                        {#<td>{{ recipe.code }}</td>#}
                        <td>{{ recipe.recipe_number }}</td>
                        <td>{{ recipe.name }}</td>
                        {#<td>
                            {% if recipe.getActive() %}
                                <a class="btn btn-warning btn-sm" href="{{ url('lab/sales_recipe/deactivate/'~recipe.getId()) }}"><i class="pe-7s-gleam"></i> {{ "Deactivate"|t }}</a>
                            {% else %}
                                <a class="btn btn-success btn-sm" href="{{ url('lab/sales_recipe/activate/'~recipe.getId()) }}"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                            {% endif %}
                        </td>#}
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/confirmRecipeInactive", ['id': 'confirm-deactive', 'title': 'Confirm deactivation recipe'|t, 'content': 'Are you sure you want to set this recipe to inactive?'|t]) }}

{% endblock %}