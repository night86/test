{% extends "layouts/main.volt" %}
{% block title %} {{'Edit user'|t}} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/user/") }}"><i class="pe-7s-back"></i></a> {{ user.getFirstname() }} {{ user.getLastname() }}</h3>
    {{ form('signadens/user/edit/' ~ user.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Identity"|t }}</legend>
        {{ hidden_field('id', 'value': user.getId()) }}
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'First name'|t}}:</label>
                    {{ text_field('firstname', 'required': 'required', 'value': user.getFirstname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Last name'|t}}:</label>
                    {{ text_field('lastname', 'required': 'required', 'value': user.getLastname(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Organisation'|t}}:</label>
                    {{ select('organisation_id', organisations, 'using': ['id', 'name'], 'required': 'required', 'value': user.getOrganisationId(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Phone'|t}}:</label>
                    {{ text_field('telephone', 'value': user.getTelephone(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Email'|t}}:</label>
                    {{ text_field('email', 'required': 'required', 'value': user.getEmail(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{'Password'|t}}:</label>
                    {{ password_field('password', 'value': '', 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{'Role'|t}}:</label>
                    <select id="role_template_id" name="role_template_id" class="form-control" required="required" {% if currentUser.Organisation.OrganisationType.getSlug() != 'signadens' %}disabled="disabled"{% endif %}>
                        <option orgType="none" selected="selected"></option>
                        {% for role in roles %}
                            <option orgType="{{ role.organisationType.getSlug() }}"  value="{{ role.id }}" {% if role.id == user.getRoleTemplateId() %}selected="selected"{% endif %}>{{ role.name }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-group">
                    <label>{{'Active'|t}}:</label>
                    {{ select('active', active, 'required': 'required', 'value': user.getActive(), 'class': 'form-control') }}
                </div>
                {% if departments is defined %}
                    <div class="form-group">
                        <label>{{'Department'|t}}:</label>
                        {% if user.getDepartment()  %}
                            {{ select('department_id', departments, 'using': ['id', 'name'], 'required': 'required', 'value': user.getDepartment().getId(), 'class': 'form-control') }}
                        {% else %}
                            {{ select('department_id', departments, 'using': ['id', 'name'], 'required': 'required', 'value': '', 'class': 'form-control') }}
                        {% endif %}
                    </div>
                {% endif %}
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            {{ text_field('hidden', 'class': 'form-control', 'style': 'visibility: hidden;') }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">&nbsp;</label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </fieldset>
    {{ end_form() }}

    <form>
        <fieldset class="form-group">
            <legend>{{'Access log'|t}}</legend>
            <table class="simple-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Date"|t }}</th>
                <th>{{ "Time"|t }}</th>
                <th>{{ "User"|t }}</th>
                <th>{{ "Action"|t }}</th>
                </thead>
                <tbody>
                {% for log in logs %}
                    <tr>
                        <td>{% if log.datetime is defined %}{{ timetostrdt(log.datetime) }}{% else %}-{% endif %}</td>
                        <td>{% if log.datetime is defined %}{{ datetimetotime(log.datetime) }}{% else %}-{% endif %}</td>
                        <td>{{ user.getFirstname() }}</td>
                        <td>{{ log.page }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </fieldset>
    </form>

{% endblock %}