{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit role"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/role/") }}"><i class="pe-7s-back"></i></a> {{ "Edit role"|t }}</h3>

    {{ form('signadens/role/edit/' ~ role.getId(), 'method': 'post', 'class': 'datatable-form') }}

    <fieldset class="form-group">
        <legend>{{ "Role"|t }}</legend>
        {{ hidden_field('id', 'value': role.getId()) }}
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{'Name'|t}}:</label>
                            {{ text_field('name', 'required': 'required', 'class': 'form-control', 'value': role.getName()) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{'Organisation type'|t}}:</label>
                            {{ select('organisation_type_id', organisation_type, 'required': 'required', 'class': 'form-control', 'value': role.getOrganisationTypeId()) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{'Description'|t}}:</label>
                            {{ text_field('description', 'class': 'form-control', 'value': role.getDescription()) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{'Active'|t}}:</label>
                                {{ select('active', active, 'required': 'required', 'class': 'form-control', 'value': role.getActive()) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{'Admin'|t}}:</label>
                                {{ select('is_admin', is_admin, 'required': 'required', 'class': 'form-control', 'value': role.getIsAdmin()) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-lg-offset-0 col-md-6 col-md-offset-6">
                <div class="row">
                    <label for="">&nbsp;</label>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <a href="{{ url('signadens/role/reset/'~role.getId()) }}" class="btn btn-warning btn-block"><i class="pe-7s-refresh-2"></i> {{'Reset role for users'|t}}</a>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-primary btn-block"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                    </div>
                </div>
            </div>

        </div>
    </fieldset>
    <fieldset class="form-group">
        <legend>{{ "Permissions"|t }}</legend>
        <div class="row">
            <div class="col-md-12">
                <table class="simple-datatable table table-striped">
                    <thead>
                    <th>{{'Name'|t}}</th>
                    <th>{{'Description'|t}}</th>
                    <th>{{'Selected'|t}}</th>
                    </thead>
                    <tbody>
                    {% for role in roles %}
                        <tr>
                            <td>{{ role.getName() }}</td>
                            <td>{{ role.getDescription() }}</td>
                            {% if in_array(role.getId(), roleRoles) %}
                                <td>{{ check_field('roles[]', 'value': role.getId(), 'checked': 'checked') }}</td>
                            {% else %}
                                <td>{{ check_field('roles[]', 'value': role.getId()) }}</td>
                            {% endif %}
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}