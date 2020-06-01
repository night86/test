{% extends "layouts/main.volt" %}
{% block title %} {{ "Tariff codes"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/invoice/add") }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Invoices"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="invoices" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Invoice No."|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th class="sortbydate">{{ "Due date"|t }}</th>
                        <th>{{ "Amount"|t }}</th>
                        <th>{{ "Added date"|t }}</th>
                        <th>{{ "Added by"|t }}</th>
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for invoice in invoices %}
                        <tr>
                            <td>{{ invoice.getNumber() }}</td>
                            <td>{{ invoice.getDescription() }}</td>
                            <td><div class="hidden">{{ invoice.getDueDate() }}</div>{{ invoice.getDueDate()|dttonl }}</td>
                            <td>&euro;{{ invoice.getAmount() }}</td>
                            <td><div class="hidden">{{ invoice.getCreatedAt() }}</div>{{ invoice.getCreatedAt()|dttonl }}</td>
                            <td>{{ invoice.CreatedBy.getFullName() }}</td>
                            <td>
                                <a href = "/signadens/invoice/edit/{{ invoice.getId() }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i> {{'Edit'|t}}</a>
                                {#<a href = "/signadens/invoice/download/{{ invoice.getId() }}" class="btn btn-warning btn-sm"><i class="pe-7s-download"></i> {{'Download'|t}}</a>#}
                                <a href = "/signadens/invoice/print/{{ invoice.getId() }}" target="_blank" class="btn btn-info btn-sm"><i class="pe-7s-print"></i> {{'Print'|t}}</a>
                                <a href = "/signadens/invoice/delete/{{ invoice.getId() }}" class="btn btn-danger btn-sm"><i class="pe-7s-trash"></i> {{'Remove'|t}}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
{% endblock %}