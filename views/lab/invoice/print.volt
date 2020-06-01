{% extends "layouts/main.volt" %}

{% block styles %}
    {{ super() }}
    <style>
        #inv-title {
            margin-bottom: 40px;
        }

        #inv-title img {
            margin: 0 auto;
            max-width: 250px;
        }

        #sidebar-wrapper {
            display: none;
        }

        #wrapper {
            padding: 0;
        }

        #page-content-wrapper header {
            display: none;
        }
        @media print {
            #print-btn {
                display: none;
            }
        }
    </style>
{% endblock %}
{% block content %}
    <button id="print-btn" onclick="printDiv('printArea')" class="btn btn-primary pull-right" style="margin-top: 15px;"><i class="pe-7s-print"></i> {{ "Print"|t }}</button>
    <div id="printArea">
        <div id="inv-title" class="row">
            <div class="col-xs-12 text-center">
                <img src="/img/Signadens-logo-def.png" class="img-responsive" alt="Signadens Logo">
            </div>
            <div class="col-xs-12 text-center">
                <h1>{{ "Invoice"|t }} {{ invoice.getNumber() }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <legend>{{ "Basic data"|t }}</legend>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <span>{{ invoice.getDescription() }}</span>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {{ invoice.getDate() }}
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {{ invoice.getDueDate() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">

                <legend class="text-right">{{ "Client data"|t }}</legend>
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            {{ invoice.getClientName() }}
                        </div>
                        <div class="form-group">
                            {{ invoice.getClientAddress() }}
                        </div>
                    </div>
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            {{ invoice.getClientNameContinue() }}
                        </div>
                        <div class="form-group">
                            {{ invoice.getClientZipCode() }}
                        </div>
                    </div>
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            {{ invoice.getClientCity() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <table id="records" class="table  table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>{{ "Amount."|t }}</th>
                        <th>{{ "Description"|t }}</th>
                        <th>{{ "Dentist"|t }}</th>
                        <th>{{ "From Lab"|t }}</th>
                        <th>{{ "Price per piece"|t }}</th>
                        <th>{{ "Total price"|t }}</th>
                        <th>{{ "BTW"|t }}</th>
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
                        </tr>
                    {% endfor %}
                    </tbody>
                </table>
            </div>

            <div class="col-xs-6 col-xs-offset-6">
                <table id="records" class="table  table-bordered" cellspacing="0" width="100%">
                    <tr>
                        <td><strong>{{ "Subtotal"|t }}:</strong></td>
                        <td>&euro;{{ invoiceValues['subtotal'] }}</td>
                    </tr>
                    {% for percentage, btw in invoiceValues['btw'] %}
                        <tr>
                            <td><strong>{{ "BTW"|t }} {{ percentage }}%:</strong></td>
                            <td> &euro;{{ btw }}</td>
                        </tr>
                    {% endfor %}
                    <tr>
                        <td><strong>{{ "Grand total"|t }}:</strong></td>
                        <td>&euro;{{ invoiceValues['grandtotal'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        function printDiv(cnt) {
                console.log('test');
                var printContents = document.getElementById(cnt).innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
        }

        $(window).load(function () {
            setTimeout(function(){
                $('#print-btn').trigger('click');
            }, 500);
        })
    </script>
{% endblock %}