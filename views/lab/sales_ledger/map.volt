{% extends "layouts/main.volt" %}
{% block title %} {{ "Map ledger codes"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("lab/sales_ledger/map") }}" class="btn-primary btn process"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</a></p>
    <h3>{{ "Map ledger codes"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="map" class="simple-datatable table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ "My ledger code"|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Map to"|t }}:</th>
                    </tr>
                </thead>
                <tbody>
                {% for ledger in ledgers %}
                    <tr>
                        <td>{{ ledger.getCode() }}</td>
                        <td>{{ ledger.getDescription() }}</td>
                        <td>
                            <select class="form-control select2-input" name="ledger-{{ ledger.getId() }}">
                                <option value="0">{{ "Select Signadens tariff code"|t }}</option>
                                {% for tarrif in tariffs %}
                                    <option value="{{ tarrif.getId() }}" {% if maps[ledger.getId()] is defined AND maps[ledger.getId()] == tarrif.getId() %}selected="selected"{% endif %}>{{ tarrif.getCode() }}{% if ledger.req is 1 %}*{% endif %}</option>
                                {% endfor %}
                            </select>
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