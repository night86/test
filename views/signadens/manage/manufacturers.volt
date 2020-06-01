{% extends "layouts/main.volt" %}
{% block title %} {{ "Manufacturers"|t }} {% endblock %}
{% block content %}

    <h3>
        {{ "Manufacturers"|t }}
        <span class="pull-right"><a id="add_manufacturer" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></span>
    </h3>

    <div class="row">
        <div class="col-md-12">
            <table class="buttons-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Manufacturer"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                {% if manufacturers is not null %}
                    {% for m in manufacturers %}
                        <tr>
                            <td>{{ m.name }}</td>
                            <td>
                                <a data-id="{{ m.id }}" data-name="{{ m.name }}" class="edit-manufacturer btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                            </td>
                        </tr>
                    {% endfor %}
                {% endif %}
                </tbody>
            </table>
        </div>
    </div>
    {{ partial("modals/addSingleField", ['id': 'add-modal', 'title': "Add a new manufacturer"|t, 'content': "Please enter the name of the new manufacturer"|t]) }}
    {{ partial("modals/editSingleField", ['id': 'edit-modal', 'title': "Edit manufacturer"|t, 'content': "Edit the name of the manufacturer"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(function(){
            $('#add_manufacturer').on('click', function(){
                //alert(code);
                $('#add-modal').modal('show');

                $('#confirmButton').on('click', function(){
                    var newName = $('#newName').val();
                    $.ajax({
                        method: 'POST',
                        url: '/signadens/manage/ajaxmanufacturers/',
                        data: {
                            newName: newName
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status != "error"){
                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/manage/manufacturers/';
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

            $('.edit-manufacturer').on('click', function(){

                $('#edit-modal').modal('show');
                $('#confirmEditButton').attr('data-id', $(this).attr('data-id'));
                $('#editName').val($(this).attr('data-name'))

                $('#confirmEditButton').on('click', function(){
                    $.ajax({
                        method: 'POST',
                        url: '/signadens/manage/ajaxmanufacturers/',
                        data: {
                            id: $(this).attr('data-id'),
                            editName: $('#editName').val()
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status != "error"){
                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/manage/manufacturers/';
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
                    $('#edit-modal').modal('hide');
                });
            });
        });
    </script>

{% endblock %}
