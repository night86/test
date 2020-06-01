{% extends "layouts/main.volt" %}
{% block title %} {{ "Add user"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Add new user"|t }}</h3>

    {{ form('lab/user/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "First name"|t }}:</label>
                    {{ text_field('firstname', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Last name"|t }}:</label>
                    {{ text_field('lastname', 'required': 'required', 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Role"|t }}:</label>
                    <select id="role_template_id_lab" name="role_template_id" class="form-control" required="required">
                        {% for role in roles %}
                            <option orgType="{{ role.organisationType.getSlug() }}"  value="{{ role.id }}">{{ role.name }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label>{{'Department'|t}}:</label>
                    <select id="department_id" name="department_id" class="form-control" required="required">
                        {% for department in departments %}
                            <option value="{{ department.id }}">{{ department.name }}</option>
                        {% endfor %}
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "Phone"|t }}:</label>
                    {{ text_field('telephone', 'class': 'form-control') }}
                </div>

                <div class="form-group">
                    <label>{{ "Email"|t }}:</label>
                    {{ text_field('email', 'required': 'required', 'class': 'form-control') }}
                </div>

                <div class="form-group">
                    <label>{{ "Active"|t }}:</label>
                    {{ select('active', active, 'required': 'required', 'class': 'form-control') }}
                </div>

                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    {{ text_field('organisation', 'required': 'required', 'value': organisation.name, 'disabled':'disabled', 'class': 'form-control') }}
                </div>

                <div class="form-group">
                   <label>&nbsp;</label>

                    {{ submit_button("Add"|t, 'class': 'btn btn-primary pull-right') }}
                </div>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}