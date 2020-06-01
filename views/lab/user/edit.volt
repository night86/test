{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit user"|t }} {% endblock %}
{% block content %}
    <h3><a href="{{ url("lab/user/") }}"><i class="pe-7s-back"></i></a><span> {{ user.getFirstname() }} {{ user.getLastname() }}</span></h3>
    {{ form('lab/user/edit/' ~ user.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        {{ hidden_field('id', 'value': user.getId()) }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "First name"|t }}:</label>
                    {{ text_field('firstname', 'required': 'required', 'value': user.getFirstname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Last name"|t }}:</label>
                    {{ text_field('lastname', 'required': 'required', 'value': user.getLastname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Role"|t }}:</label>
                    {{ select('role_template_id', roles, 'using': ['id', 'name'], 'required': 'required', 'value': user.getRoleTemplateId(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    {{ text_field('organisation', 'required': 'required', 'value': organisation.name, 'disabled':'disabled', 'class': 'form-control') }}
                </div>
                {% if departments is defined %}
                    <div class="form-group">
                        <label>{{'Department'|t}}:</label>
                        {{ select('department_id', departments, 'using': ['id', 'name'], 'required': 'required', 'value': user.getDepartmentName(), 'class': 'form-control') }}
                    </div>
                {% endif %}
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('telephone', 'value': user.getTelephone(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Email"|t }}:</label>
                    <label>{{ "Email"|t }}:</label>
                    {{ text_field('email', 'required': 'required', 'value': user.getEmail(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Password"|t }}:</label>
                    {{ password_field('password', 'value': '', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Active"|t }}:</label>
                    {{ select('active', active, 'required': 'required', 'value': user.getActive(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    {{ submit_button('Save'|t, 'class': 'btn btn-primary pull-right') }}
                </div>
            </div>
        </div>


    </fieldset>
    {{ end_form() }}
    {#
    <form>
        <fieldset class="form-group">
            <legend>{{ "Access log"|t }}</legend>
            <table class="simple-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Date"|t }}</th>
                <th>{{ "Time"|t }}</th>
                <th>{{ "User"|t }}</th>
                <th>{{ "Action"|t }}</th>
                </thead>
                <tbody>
                <tr>
                    <td>test</td>
                    <td>test</td>
                    <td>test</td>
                    <td>test</td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </form>
    #}
{% endblock %}