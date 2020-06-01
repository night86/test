{% extends "layouts/main.volt" %}
{% block title %} {{ "Add new role"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new role"|t }}</h3>

    {{ form('signadens/role/add', 'method': 'post') }}
    {#{{ submit_button('Add', 'class': 'btn btn-primary pull-right') }}#}
    <fieldset class="form-group">
        <legend>{{ "Role"|t }}</legend>
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ "Name"|t }}:</label>
                            {{ text_field('name', 'required': 'required', 'class': 'form-control') }}
                        </div>

                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ "Organisation type"|t }}:</label>
                            <select name="organisation_type_id" id="organisation_type_id" class="form-control" required="required">
                                {% for key, organisation in organisation_type %}
                                    <option value="{{ key }}">{{ organisation|t }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ "Description"|t }}:</label>
                            {{ text_field('description', 'class': 'form-control') }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{'Active'|t}}:</label>
                                {{ select('active', active, 'required': 'required', 'class': 'form-control') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{'Admin'|t}}:</label>
                                {{ select('is_admin', is_admin, 'required': 'required', 'class': 'form-control') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="row">
                    <label for="">&nbsp;</label>
                </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
            </div>
        </div>
    </fieldset>
    <fieldset class="form-group">
        <legend>{{ "Permissions"|t }}</legend>
        <div class="row">
            <div class="col-md-12">
                <table class="simple-datatable table table-striped">
                    <thead>
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th>{{ "Selected"|t }}</th>
                    </thead>
                    <tbody>
                    {% for role in roles %}
                        <tr>
                            <td>{{ role.getName() }}</td>
                            <td>{{ role.getDescription() }}</td>
                            <td>{{ check_field('roles['~role.getId()~']', 'value': role.getId()) }}</td>
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Confirmation"|t, 'content': "Are you sure you want to leave?"|t]) }}

{% endblock %}
{% block scripts %}
    {{ super() }}
        <script>
            $(function(){
                $('a').on('click', function(e){
                    e.preventDefault();
                    var redirectUrl = $(this).attr('href');
                    var confirmModal = $('#confirm-modal');
                    confirmModal.modal('show');

                    $('.confirm-button').on('click', function(){
                        confirmModal.modal('hide');
                        window.location = redirectUrl;
                    });
                });
            });
        </script>
{% endblock %}