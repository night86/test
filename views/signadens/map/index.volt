{% extends "layouts/main.volt" %}
{% block title %} {{ "Ledger codes"|t }} {% endblock %}
{% block content %}

    <p class="pull-right"><a href="{{ url("signadens/map/") }}" class="btn-primary btn process"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</a></p>
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
                                <select class="form-control" name="ledger-{{ ledger.getId() }}">
                                    <option value="0">{{ "Select Signadens tariff code"|t }}</option>
                                    {% for tarrif in tariffs %}
                                        <option value="{{ tarrif.getId() }}" {% if maps[ledger.getId()] is defined AND maps[ledger.getId()] == tarrif.getId() %}selected="selected"{% endif %}>{{ tarrif.getCode() }}</option>
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

    <script>
        map.saveData('{{ "Maps have been saved."|t }}', '{{ "None map has been created or updated."|t }}');
    </script>
{% endblock %}