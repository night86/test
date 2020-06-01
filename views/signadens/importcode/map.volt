{% extends "layouts/main.volt" %}
{% block title %} {{ "Import"|t }} {% endblock %}
{% block content %}

    <div class="col-lg-12">
        <h3>{{ "Import"|t }}</h3>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6" style="margin-bottom: 20px;">
            {{ select('map[]', allMaps, 'using': ['id', 'file'], 'useEmpty': true, 'emptyText': 'Select culumns map', 'emptyValue': 0, 'class': 'form-control', 'data-url':url('signadens/importcode/ajaxmap/'), 'id':'columns-maps') }}
        </div>
    </div>
    <div class="import-steps">
        <div class="row">
            <div class="col-md-12">
                <ul class="step-list">
                    <li {% if router.getActionName() === 'index' %} class="active" {% endif %}>
                        1. {{ "Select import type and file"|t }}</li>
                    <li {% if router.getActionName() === 'map' %} class="active" {% endif %}>
                        2. {{ "Map columns"|t }}</li>
                    <li {% if router.getActionName() === 'overview' %} class="active" {% endif %}>
                        3. {{ "Import overview"|t }}</li>
                    <li {% if router.getActionName() === 'confirm' %} class="active" {% endif %}>
                        4. {{ "Affected rows"|t }}</li>
                    <li {% if router.getActionName() === 'complete' %} class="active" {% endif %}>
                        5. {{ "Ready for approval"|t }}</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ form('signadens/importcode/map', 'method': 'post') }}
                <fieldset class="form-group">
                    <table class="table table-striped">
                        <thead>
                        <th>{{ "Column ID"|t }}</th>
                        <th>{{ "Column name"|t }}</th>
                        <th>{{ "Column value"|t }}</th>
                        <th>{{ "Map to:"|t }}</th>
                        </thead>
                        <tbody>
                        {% for index, header in headers %}
                            <tr>
                                <td>{{ index }}</td>
                                <td>{{ header }}</td>
                                <td>{{ column[index]|truncate }}</td>
                                <td>
                                    <select name="map[]" class="form-control" id="map-{{ index }}" required="required">
                                        <option value="0">{{ "Skip column"|t }}</option>
                                        {% for mapValue in maps %}
                                            <option value="{{ mapValue.id }}" {% if map[index] is defined AND map[index] == mapValue.id %} selected {% endif %}>{{ mapValue.description|t }}</option>
                                        {% endfor %}
                                    </select>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                    <div class="col-lg-12">
                        <a href="{{ url('signadens/importcode/') }}" class="btn btn-primary pull-left"><i class="pe-7s-angle-left"></i> {{ "Previous step"|t }}
                            </a>
                        {#{{ submit_button('Next step >', 'class': 'btn btn-primary pull-right') }}</div>#}
                        <button type="submit" class="btn btn-primary pull-right">{{ "Next step"|t }} <i class="pe-7s-angle-right"></i></button>
                </fieldset>
                {{ end_form() }}
            </div>
        </div>
    </div>

{% endblock %}
{% block scripts %}
    {{ super() }}
    <script type="text/javascript">
        $(document).ready(function () {
            // Change columns maps
            $('#columns-maps').on('change', function () {
                $.get($(this).data('url') + $(this).val())
                .done(function (data) {
                    var results = JSON.parse(data);
                    for (i = 0; i < results.length; i++) {
                        $("#map-" + i).val(results[i]).change();
                    }
                })
            });
        });
    </script>
{% endblock %}