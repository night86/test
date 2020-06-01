{% extends "layouts/main.volt" %}
{% block title %} {{ "Add role"|t }} {% endblock %}
{% block content %}
    {{ form('supplier/role/add', 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Role"|t }}</legend>
        <div class="row">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ "Name"|t }}:</label>
                            {{ text_field('name', 'required': 'required', 'class': 'form-control') }}
                        </div>
                        <input type="hidden" name="organisation_type_id" value="{{ currentOrganisationType }}"/>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ "Description"|t }}:</label>
                            {{ text_field('description', 'class': 'form-control') }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ "Active"|t }}:</label>
                            {{ select('active', active, 'required': 'required', 'class': 'form-control') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <label for="">&nbsp;</label>
                <p><button class="btn-primary btn btn-block"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</button></p>
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
                                <td>{{ check_field('roles['~role.getId()~']', 'class': 'form-control', 'value': role.getId()) }}</td>
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