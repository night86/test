{% extends "layouts/main.volt" %}
{% block title %} {{ "Recipes"|t }} {% endblock %}
{% block content %}

    <h3>
        {{ "Recipes"|t }}
        <span class="pull-right"><a href="{{ url("signadens/product/add") }}" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table id="clients" class="buttons-datatable-recipes table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Code"|t }}</th>
                    <th>{{ "Recipe number"|t }}</th>
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                    {% for recipe in recipes %}
                        <tr>
                            <td>{{ recipe.code }}</td>
                            <td>{{ recipe.recipe_number }}</td>
                            <td>{{ recipe.name }}</td>
                            <td>
                                <a href="{{ url('signadens/product/edit/' ~ recipe.code) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                                <a class="btn btn-info btn-sm duplicate-button" data-code="{{ recipe.code }}"><i class="pe-7s-copy-file"></i> {{ 'Duplicate'|t }}</a>
                                {% if recipe.active %}
                                    <a href="{{ url('signadens/product/deactivate/' ~ recipe.code) }}" class="btn btn-warning btn-sm"><i class="pe-7s-close-circle"></i> {{'Deactivate'|t}}</a>
                                {% else %}
                                    <a href="{{ url('signadens/product/activate/' ~ recipe.code) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{'Activate'|t}}</a>
                                {% endif %}
                                <a href="{{ url('signadens/product/delete/' ~ recipe.code) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{'Delete'|t}}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
    {{ partial("modals/confirmRecipeInactive", ['id': 'confirm-deactive', 'title': 'Confirm deactivation recipe'|t, 'content': 'Are you sure you want to set this recipe to inactive? Labs will be notified about this and it will no longer be possible for dentists to order this recipe.'|t]) }}
    {{ partial("modals/addSingleField", ['id': 'add-modal', 'title': "Create recipe copy"|t, 'content': "Please enter the name of the new recipe"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(function(){
            $('.duplicate-button').on('click', function(){
                var code = $(this).attr('data-code');
                //alert(code);
                $('#add-modal').modal('show');

                $('#confirmButton').on('click', function(){
                    var newName = $('#newName').val();
                    $.ajax({
                        method: 'POST',
                        url: '/signadens/product/duplicate/',
                        data: {
                            code: code,
                            newName: newName
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status != "error"){
                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/product/';
                                    }, 1000);
                                }, 1000);
                            }
                            else {
                                setTimeout(function () {
                                    toastr.error(obj.msg);
                                }, 1000);
                            }
                        }
                    });
                    $('#add-modal').modal('hide');
                });


            });
        });
    </script>

{% endblock %}