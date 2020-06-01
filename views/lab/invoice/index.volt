{% extends "layouts/main.volt" %}
{% block title %} {{ "Invoices"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a class="btn-primary btn modalInvoice"><i class="pe-7s-plus"></i> {{ "Generate new invoices"|t }}</a></p>
    <h3>{{ "Invoices"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="invoices" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="sortbydate">{{ "Invoice Date"|t }}</th>
                        <th>{{ "Status"|t }}</th>
                        <th>{{ "Invoice number(s)"|t }}</th>
                        <th>{{ "Invoice period"|t }}</th>
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for invoice in invoicesBulk %}
                        <tr>
                            <td><div class="hidden">{{ invoice['date'] }}</div>{{ invoice['date']|dttonl }}</td>
                            <td>{{ invoice['bulk_status'] }}</td>
                            {% if invoice['bulk_status'] == 'processed' %}
                                <td>{{ invoice['first_invoice']['number'] }}{% if isset(invoice['last_invoice']['number']) and invoice['last_invoice']['number'] is not null %} - {{ invoice['last_invoice']['number'] }}{% endif %}</td>
                            {% else %}
                                <td>{{ invoice['bulk_status'] }}</td>
                            {% endif %}
                            <td>{{ invoice['start_period'] }} - {{ invoice['end_period'] }}</td>
                            <td>
                                {% if invoice['bulk_status'] != 'processed' %}
                                <a data-url="/lab/invoice/processbulk/{{ invoice['id'] }}" class="btn btn-primary btn-sm bulkAction"><i class="pe-7s-shuffle"></i> {{'Process'|t}}</a>
                                {% endif %}
                                <a href="/lab/invoice/downloadzip/{{ invoice['id'] }}" target="_blank" class="btn btn-info btn-sm"><i class="pe-7s-download"></i> {{'Download zip'|t}}</a>
                                <a data-url="/lab/invoice/deletebulk/{{ invoice['id'] }}" class="btn btn-danger btn-sm bulkAction"><i class="pe-7s-trash"></i> {{'Delete'|t}}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
    {{ partial("modals/addInvoices", ['id': 'modalInvoice', 'title': 'Generate new invoices'|t]) }}
    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Confirmation"|t, 'content': "Are you sure?"|t, 'confirmButton': 'Yes, I am sure'|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}

{% endblock %}