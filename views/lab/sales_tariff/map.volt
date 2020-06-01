{% extends "layouts/main.volt" %}
{% block title %} {{ "Map tariff codes"|t }} {% endblock %}
{% block content %}
    {{ form('lab/sales_tariff/map', 'method': 'post') }}
    <p class="pull-right"><button type="submit" class="btn-primary btn process"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button></p>
    <h3>{{ "Map tariff codes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="map" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>{{ "My tariff code"|t }}</th>
                    <th>{{ "Description"|t }}</th>
                    <th>{{ "Price"|t }}</th>
                    <th>{{ "Map to tariff code"|t }}</th>
                    <th>{{ "or product"|t }}</th>
                    <th>{{ "Overrule Ledger"|t }}</th>
                </tr>
                </thead>
                <tbody>

                {% for tariff in tariffs %}
                    <tr>
                        <td>{{ tariff.getCode() }}</td>
                        <td>{{ tariff.getDescription() }}</td>
                        <td>{{ tariff.getPrice() }}</td>
                        <td>
                            <select class="form-control select2-input" name="tariff-{{ tariff.getId() }}">
                                <option value="0">{{ "Select Signadens tariff code"|t }}</option>
                                {% for signaTariff in signaTariffs %}
                                    <option value="{{ signaTariff.getId() }}"{% if maps[tariff.getId()] is defined AND maps[tariff.getId()]['tariff'] is signaTariff.getId() %}selected="selected"{% endif %}>{{ signaTariff.getCode() ~ ' - ' ~ signaTariff.getDescription() }}</option>
                                {% endfor %}
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2-input" name="product-{{ tariff.getId() }}">
                                <option value="0">{{ "Select product from shortlist"|t }}</option>
                                {% for shortlist in shortlists %}
                                    <option value="{{ shortlist.getProductId() }}"{% if maps[tariff.getId()] is defined AND maps[tariff.getId()]['product'] is shortlist.getProductId() %}selected="selected"{% endif %}>{{ shortlist.Product.getName() }} (from: {{ shortlist.Product.Organisation.getName() }}, price: &euro;{{ shortlist.Product.getPrice() }})</option>
                                {% endfor %}
                            </select>
                        </td>

                        <td>
                            <select class="form-control select2-input" name="ledger-{{ tariff.getId() }}">
                                <option value="0">{{ "Select ledger number"|t }}</option>
                                {% for ledger in ledgers %}
                                    <option value="{{ ledger.getId() }}"{% if maps[tariff.getId()] is defined AND maps[tariff.getId()]['ledger'] is ledger.getId() %}selected="selected"{% endif %}>{{ ledger.getCode() ~ ' - '~ ledger.getDescription()}}</option>
                                {% endfor %}
                            </select>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
    {{ end_form() }}

{% endblock %}