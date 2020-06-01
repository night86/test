{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit role"|t }} {% endblock %}
{% block content %}
    {{ form('supplier/role/edit', 'method': 'post') }}
    {{ submit_button('Save', 'class': 'btn btn-primary pull-right') }}
    <a href="{{ url('supplier/role/reset/'~role.getId()) }}" class="btn btn-default pull-right">Reset role for users</a>
    <fieldset class="form-group">
        <legend>{{ "Role"|t }}</legend>
        {{ hidden_field('id', 'value': role.getId()) }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "Name"|t }}:</label>
                    {{ text_field('name', 'required': 'required', 'class': 'form-control', 'value': role.getName()) }}
                </div>
                <input type="hidden" name="organisation_type_id" value="{{ currentOrganisationType }}" />
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'class': 'form-control', 'value': role.getDescription()) }}
                </div>
                <div class="form-group">
                    <label>{{ "Active"|t }}:</label>
                    {{ select('active', active, 'required': 'required', 'class': 'form-control', 'value': role.getActive()) }}
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
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th>{{ "Selected"|t }}</th>
                    </thead>
                    <tbody>
                    {% for role in roles %}
                        {% if organisationSlug()|upper in role.getName() %}
                            <tr>
                                <td>{{ role.getName() }}</td>
                                <td>{{ role.getDescription() }}</td>
                                {% if in_array(role.getId(), roleRoles) %}
                                    <td>{{ check_field('roles[]', 'class': 'form-control', 'value': role.getId(), 'checked': 'checked') }}</td>
                                {% else %}
                                    <td>{{ check_field('roles[]', 'class': 'form-control', 'value': role.getId()) }}</td>
                                {% endif %}
                            </tr>
                        {% endif %}
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    {{ end_form() }}

{% endblock %}