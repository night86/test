{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit invoice"|t }} {% endblock %}
{% block content %}

    <h3><a href="{{ url("signadens/invoice/") }}"><i class="pe-7s-back"></i></a> {{ "Edit invoice"|t }}</h3>

    {{ form('signadens/invoice/edit/'~invoice.getId(), 'method': 'post') }}

    <fieldset class="form-group">
        <legend>{{ "Basic data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Description"|t }}:</label>
                    {{ text_field('description', 'required': 'required', 'value': invoice.getDescription(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Invoice date"|t }}:</label>
                    {{ text_field('date', 'class': 'form-control datepicker', 'value': invoice.getDate(), 'required': 'required') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Due date"|t }}:</label>
                    {{ text_field('due_date', 'class': 'form-control datepicker', 'value': invoice.getDueDate(), 'required': 'required') }}
                </div>
            </div>
        </div>


    </fieldset>
    <legend>{{ "Client data"|t }}</legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client name"|t }}:</label>
                    {{ text_field('client_name', 'required': 'required', 'value': invoice.getClientName(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client address"|t }}:</label>
                    {{ text_field('client_address', 'required': 'required', 'value': invoice.getClientAddress(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client name continue"|t }}:</label>
                    {{ text_field('client_name_continue', 'value': invoice.getClientNameContinue(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client zip code"|t }}:</label>
                    {{ text_field('client_zip_code', 'required': 'required', 'value': invoice.getClientZipCode(), 'class': 'form-control') }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{ "Client city"|t }}:</label>
                    {{ text_field('client_city', 'required': 'required', 'value': invoice.getClientCity(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <label>{{ "Client country"|t }}:</label>
                    {{ text_field('client_country', 'required': 'required', 'value': invoice.getClientCountry(), 'class': 'form-control') }}
                </div>
                <div class="form-group">
                    <input type="hidden" name="invoice_type" value="signa" />
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

    <div class="row">
        <div class="col-md-12">
            <p><a href="{{ url("signadens/invoice/addrecord/"~invoice.getId()) }}" class="btn-primary btn "><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
            <table id="records" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "Amount."|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Dentist"|t }}</th>
                        <th>{{ "From Lab"|t }}</th>
                        <th>{{ "Price per piece"|t }}</th>
                        <th>{{ "Total price"|t }}</th>
                        <th>{{ "BTW"|t }}</th>
                        <th>{{ "Actions"|t }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for record in records %}
                        <tr>
                            <td>{{ record.getAmount() }}</td>
                            <td>{{ record.getDescription() }}</td>
                            <td>{{ record.getReceiver() }}</td>
                            <td>{{ record.getSender() }}</td>
                            <td>&euro;{{ record.getPrice() }}</td>
                            <td>&euro;{{ record.getPrice() * record.getAmount() }}</td>
                            <td>{{ record.getTax() }}%</td>
                            <td>
                                <a class="btn btn-primary" href = "/signadens/invoice/editrecord/{{ invoice.getId() }}?recordId={{ record.getId() }}"><i class="pe-7s-pen"></i> {{ 'Edit'|t }}</a>
                                <a class="btn btn-danger" href = "/signadens/invoice/deleterecord/{{ invoice.getId() }}?recordId={{ record.getId() }}"><i class="pe-7s-trash"></i> {{ 'Remove'|t }}</a>
                            </td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
            <div class="col-md-6 col-md-offset-6">
                <p><strong>{{ "Subtotal"|t }}:</strong> &euro;{{ invoiceValues['subtotal'] }}</p>
                {% for percentage, btw in invoiceValues['btw'] %}
                    <p><strong>{{ "BTW"|t }} {{ percentage }}%:</strong> &euro;{{ btw }}</p>
                {% endfor %}
                <p><strong>{{ "Grand total"|t }}:</strong> &euro;{{ invoiceValues['grandtotal'] }}</p>
            </div>

        </div>
    </div>

{% endblock %}