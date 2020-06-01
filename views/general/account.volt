{% extends "layouts/main.volt" %}
{% block title %} {{ "Account information"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Account information"|t }}</h3>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h5>{{ "Basic data"|t }}</h5>
            <div class="dataTables_wrapper">
                <p><span class="row-data">{{ "First name"|t }}: </span>{{ user.getFirstname() }}</p>
                <p><span class="row-data">{{ "Last name"|t }}: </span>{{ user.getLastname() }}</p>
                <p><span class="row-data">{{ "Phone"|t }}: </span>{{ user.getTelephone() }}</p>
                <p><span class="row-data">{{ "Email"|t }}: </span>{{ user.getEmail() }}</p>
                <p><span class="row-data">{{ "Address"|t }}: </span>{{ user.getAddress() ~ ' ' ~ user.getZipCode() ~ ' ' ~ user.getCity() }}</p>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <h5>{{ "Access log"|t }}</h5>
            <table class="simple-datatable table table-striped">
                <thead>
                    <th class="sortbydate">{{ "Date"|t }}</th>
                    <th>{{ "Time"|t }}</th>
                    <th>{{ "Page"|t }}</th>
                    <th>{{ "State"|t }}</th>
                </thead>
                <tbody>
                    {% for log in logs %}
                        <tr>
                            <td>{% if log.datetime is defined %}{{ timetostrdt(log.datetime) }}{% else %}-{% endif %}</td>
                            <td>{% if log.datetime is defined %}{{ datetimetotime(log.datetime) }}{% else %}-{% endif %}</td>
                            <td>{% if log.page is defined %}{{ log.page }}{% else %}-{% endif %}</td>
                            <td>{% if log.state is defined %}{{ log.state|t }}{% else %}-{% endif %}</td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}