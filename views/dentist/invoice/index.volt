{% extends "layouts/main.volt" %}
{% block title %} {{ "Invoices"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Invoices"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="invoices" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Lab name"|t }}</th>
                        <th class="sortbydate">{{ "Invoice Date"|t }}</th>
                        <th>{{ "Invoice number"|t }}</th>
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for invoice in invoices %}
                        <tr>
                            <td>{{ invoice.Seller.name }}</td>
                            <td><div class="hidden">{{ invoice.date }}</div>{{ invoice.date|dttonl }}</td>
                            <td>{{ invoice.number }}</td>
                            <td>
                                <a href="/dentist/invoice/download/{{ invoice.id }}" target="_blank" class="btn btn-info btn-sm"><i class="pe-7s-download"></i> {{'Download invoice'|t}}</a>
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