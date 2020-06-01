{% extends "layouts/main.volt" %}
{% block title %} {{ "Clients"|t }} {% endblock %}
{% block content %}

    <p class="pull-right">
        <a href="/lab/sales_client/" class="btn btn-default navbar-btn pull-right"><i class="pe-7s-id"></i> {{ "Back to clients"|t }}</a>
    </p>
    <h3>{{ "Pending invites"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="clients" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <th>{{ "Email"|t }}</th>
                    <th>{{ "Organisation data"|t }}</th>
                    <th>{{ "Client number"|t }}</th>
                    <th>{{ "Created at"|t }}</th>
                    <th>{{ "Valid until"|t }}</th>
                    <th>{{ "Actions"|t }}</th>
                </thead>
                <tbody>
                    {% for invite in invites %}
                        <tr>
                            <td>{{ invite.getEmail() }}</td>
                            <td>{{ invite.getOrganisationData() }}</td>
                            <td>{{ invite.getClientNumber() }}</td>
                            <td>{{ invite.getCreatedAt() }}</td>
                            <td>{{ invite.getValidTill() }}</td>
                            <td>
                                <a href="/lab/sales_client/editinvite/{{ invite.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{ "Edit"|t }}</a>
                                {#<a href="{{ url('lab/sales_client/loginasuser/'~client.Dentist.getId()) }}" class="btn btn-warning btn-sm"><i class="pe-7s-glasses"></i> {{ "Login as"|t }}</a>#}
                                {#{% if client.Dentist.getActive() %}#}
                                    {#<a href="{{ url('lab/sales_client/deactivate/'~client.Dentist.getId()) }}" class="btn btn-danger btn-sm"><i class="pe-7s-close-circle"></i> {{ "Deactivate"|t }}</a>#}
                                {#{% else %}#}
                                    {#<a href="{{ url('lab/sales_client/activate/'~client.Dentist.getId()) }}" class="btn btn-success btn-sm"><i class="pe-7s-gleam"></i> {{ "Activate"|t }}</a>#}
                                {#{% endif %}#}
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}