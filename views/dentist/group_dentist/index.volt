{% extends "layouts/main.volt" %}
{% block title %} {{'Users'|t}} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("dentist/group_dentist/") }}" id="invite-button" class="btn-primary btn"><i class="pe-7s-plus"></i> {{ "Invite dentist"|t }}</a></p>
    <h3>{{ "Manage dentist"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table class="simple-datatable table table-striped">
                <thead>
                    <th>{{ "Active"|t }}</th>
                    <th>{{ "Name"|t }}</th>
                    <th>{{ "Address"|t }}</th>
                    <th>{{ "Zip code"|t }}</th>
                    <th>{{ "City"|t }}</th>
                    <th>{{ "Country"|t }}</th>
                    <th>{{ "Phone"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                    {% for user in users %}
                        <tr>
                            <td>{{ active[user.getActive()]|t }}</td>
                            <td>{{ user.getFullName() }}</td>
                            <td>{{ user.getAddress() }}</td>
                            <td>{{ user.getZipCode() }}</td>
                            <td>{{ user.getCity() }}</td>
                            <td>{{ user.getCountry() }}</td>
                            <td>{{ user.getTelephone() }}</td>
                            <td>
                                <a href="{{ url("dentist/group_dentist/edit/" ~ user.getId()) }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                                {% if user.getActive() == 1 %}
                                    <a href="{{ url("dentist/group_dentist/deactivate/" ~ user.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>
                                {% else %}
                                    <a href="{{ url("dentist/group_dentist/activate/" ~ user.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>
                                {% endif %}
                                <a href="{{ url("dentist/group_dentist/loginasuser/" ~ user.getId()) }}" class="btn btn-warning btn-sm"><i class="pe-7s-glasses"></i> {{ "Login as"|t }}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

    {{ partial("modals/confirm", ['id': 'add-client', 'title': 'Invite dentist as new client'|t, 'content': inviteContent, 'additionalClass': 'send-invite', 'skiptranslation': true]) }}
    {{ partial("modals/alert", ['id': 'sended-message', 'title': 'Success'|t, 'content': 'Message has been sended to the denstist.'|t]) }}
    {{ partial("modals/alert", ['id': 'not-sended-message', 'title': 'Warning'|t, 'content': 'An error occurred while sending the message.'|t]) }}

{% endblock %}