{% extends "layouts/main.volt" %}
{% block title %} {{ "Organisation"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Organisation"|t }}</h3>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h5>{{ "Basic data"|t }}</h5>
            <div class="dataTables_wrapper">
                <p><span class="row-data">{{ "Name"|t }}: </span>{{ organisation.getName() }}</p>
                <p><span class="row-data">{{ "Phone"|t }}: </span>{{ organisation.getTelephone() }}</p>
                <p><span class="row-data">{{ "Email"|t }}: </span>{{ organisation.getEmail() }}</p>
                <p><span class="row-data">{{ "Address"|t }}
                        : </span>{{ organisation.getAddress() ~ ' ' ~ organisation.getZipCode() ~ ' ' ~ organisation.getCity() }}
                </p>
                {% if currentUser.hasRole('ROLE_SUPPLIER_GENERAL_ORGANISATION_EDIT')
                    or currentUser.hasRole('ROLE_LAB_GENERAL_ORGANISATION_EDIT')
                    or currentUser.hasRole('ROLE_DENTIST_GENERAL_ORGANISATION_EDIT')
                    or currentUser.hasRole('ROLE_SIGNADENS_GENERAL_ORGANISATION_EDIT')
                %}
                    <a href="{{ url('general/organisationEdit/' ~ organisation.getId()) }}" class="btn btn-primary"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                {% endif %}
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <h5>{{ "Employees"|t }}</h5>
            <table class="simple-datatable table table-striped">
                <thead>
                <th>{{ "Name"|t }}</th>
                <th>{{ "Email"|t }}</th>
                <th>{{ "Phone"|t }}</th>
                </thead>
                <tbody>
                {% for user in users %}
                    <tr>
                        <td>{{ user.getFullname() }}</td>
                        <td>{{ user.getEmail() }}</td>
                        <td>{{ user.getTelephone() }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}