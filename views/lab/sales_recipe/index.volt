{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Map recipes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="recipes" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    {#<th>{{ "Signa No."|t }}</th>#}
                    <th>{{ "Recipe number"|t }}</th>
                    <th>{{ "Recipe name"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th>{{ "Price type"|t }}</th>
                    <th>{{ "My recipe code"|t }}</th>
                    <th>{{ "My recipe name"|t }}</th>
                    <th>{{ "Status"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                    {% for recipe in recipes %}
                        <tr>
                            {% if recipe.Lab and currentUser.Organisation.id is recipe.Lab.id %}
                                {#<td>{{ recipe.ParentRecipe.getCode() }}</td>#}
                                <td>{{ recipe.ParentRecipe.getRecipeNumber() }}</td>
                                <td>{{ recipe.ParentRecipe.getName() }}</td>
                                <td>{{ recipe.ParentRecipe.getDescription() }}</td>
                                <td>
                                    {% if recipe.getPriceType() == "Composite" %}
                                        {{ "Composite"|t }}
                                    {% else %}
                                        {{ "Fixed"|t }}
                                    {% endif %}
                                </td>
                                <td><input id="code_{{ recipe.id }}" class="recipe-code" type="text" name="code" value="{{ recipe.custom_code }}" placeholder="{{ 'Enter code...'|t }}"></td>
                                <td><input id="name_{{ recipe.id }}" class="recipe-name" type="text" name="name" value="{{ recipe.custom_name }}" placeholder="{{ 'Enter name...'|t }}"></td>
                                <td>
                                    {% if recipe.getActive() %}
                                        {{ "active"|t }}
                                    {% else %}
                                        {{ "inactive"|t }}
                                    {% endif %}
                                </td>
                                <td>
                                    <a data-id="{{ recipe.id }}" class="btn btn-default btn-sm save-row" href="javascript:;"><i class="pe-7s-pen"></i> {{ "Save"|t }}</a>
                                    <a class="btn btn-primary btn-sm" href="{{ url('lab/sales_recipe/edit/'~recipe.getCode()) }}"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>

                                </td>
                            {% else %}
                                <td>{{ recipe.getRecipeNumber() }}</td>
                                <td>{{ recipe.getName() }}</td>
                                <td>{{ recipe.getDescription() }}</td>
                                <td></td>
                                <td><input id="code_{{ recipe.id }}" class="recipe-code" type="text" name="code" value="" placeholder="{{ 'Enter code...'|t }}"></td>
                                <td><input id="name_{{ recipe.id }}" class="recipe-name" type="text" name="name" value="" placeholder="{{ 'Enter name...'|t }}"></td>
                                <td></td>
                                <td>
                                    <a data-id="{{ recipe.id }}" class="btn btn-success btn-sm save-row" href="javascript:;"><i class="pe-7s-pen"></i> {{ "Save"|t }}</a>
                                </td>
                            {% endif %}
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>

        $('.save-row').on('click', function(){

            var code = $('#code_'+$(this).attr('data-id'));
            var name = $('#name_'+$(this).attr('data-id'));

            if (code.val() === '') { toastr.error('{{ 'Your code can not be empty.'|t }}'); return; }
            if (name.val() === '') { toastr.error('{{ 'Your name can not be empty.'|t }}'); return; }

            if(code.val() !== '' && name.val() !== ''){
                $.ajax({
                    url: '/lab/sales_recipe/saverow',
                    type: 'post',
                    data: {
                        'id': $(this).attr('data-id'),
                        'code': code.val(),
                        'name': name.val()
                    },
                    success: function (data) {
                        var obj = $.parseJSON(data);
                        if (obj.status == 'success') {
                            setTimeout(function () {
                                toastr.success(obj.message);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            }, 1000);
                        } else {
                            setTimeout(function () {
                                toastr.error(data.message)
                            }, 1000);
                        }
                    }
                });
            }
        });

    </script>
{% endblock %}