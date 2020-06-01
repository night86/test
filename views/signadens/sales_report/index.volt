{% extends "layouts/main.volt" %}
{% block title %} {{ 'Dashboard'|t }} {% endblock %}
{% block content %}

    <h3>{{ "Sales report"|t }}</h3>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <form id="salesReportFrom" action="{{ url('signadens/sales_report/') }}" method="post"
                  enctype="multipart/form-data">
                <fieldset class="form-group">
                    <legend></legend>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date-from">From: </label>
                                <input id="date-from" name="from" type="text" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date-to">To: </label>
                                <input id="date-to" name="to" type="text" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group" style="color: #A8A8A7; font-size: 14px; font-weight: bold">
                                CODES TYPE:
                                <br>
                                <label for="tariff"
                                       style="color:#000; font-weight: normal; text-transform: none">Tariff </label>
                                <input id="tariff" name="code" type="radio" value="tariff" checked>
                                <br>
                                <label for="ledger"
                                       style="color:#000; font-weight: normal; text-transform: none">Ledger </label>
                                <input id="ledger" name="code" type="radio" value="ledger">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="codes">Select Code: </label>
                                <select id="codes" name="codes" multiple="multiple">
                                    <option id="all-codes">All</option>
                                    {% if ledgerCodes is not null %}
                                    {% for ledger in ledgerCodes %}
                                    <option id="{{ ledger.id }}">{{ ledger.code }} - {{ ledger.description }} </option>
                                    {% endfor %}
                                    {% endif %}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="lab">Laboratory: </label>
                                <select id="lab" name="lab">
                                    <option id="all-labs" >All</option>
                                    {% if labs is not null %}
                                        {% for lab in labs %}
                                            <option id="{{ lab.id }}">{{ lab.name }}</option>
                                        {% endfor %}
                                    {% endif %}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <input type="submit" value="Create Report">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="allcodes" name="allcodes" value="">
                </fieldset>
            </form>
            <table id="importlog" class="buttons-datatable table table-striped">
                <thead>
                <th class="sortbydate">{{ "Periode"|t }}</th>
                <th>{{ "Tariff code"|t }}</th>
                <th>{{ "Amount"|t }}</th>
                <th> {{ "Value"|t }}</th>
                </thead>
                <tbody>
                {% if reports is defined %}
                    {% for report in reports %}
                        <tr>
                            <td>{% if report.periode is defined %} {{ report.periode }} {% else %}-{% endif %}
                            <td>{% if report.tariff is defined %} {{ report.tariff }} {% else %}-{% endif %}</td>
                            <td>{% if report.amount is defined %} {{ report.amount }} {% else %}-{% endif %}</td>
                            <td>{% if report.value is defined %} {{ report.value }} {% else %}-{% endif %}</td>
                        </tr>
                    {% endfor %}
                {% endif %}
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        $( document ).ready(function () {
            var codes;
            $("#codes").select2();
            $( ".datepicker" ).datepicker();
            $('#date-from, #date-to').datepicker({
                showOn: "both",
                beforeShow: customRange,
                dateFormat: "dd-M-yy",
            });
            function customRange(input) {
                console.log('function');
                if (input.id === 'date-to') {
                    var minDate = new Date($('#date-from').val());
                    minDate.setDate(minDate.getDate() + 1)

                    return {
                        minDate: minDate
                    };
                }

                return {}
            }
            $("#codes").change(function () {
                codes = '';
                $('.select2-selection__choice').each(function (index) {
                 codes+=$(this).text();
                });
                $('#allcodes').val(codes);
            });


        });
    </script>
{% endblock %}