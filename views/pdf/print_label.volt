<html>
<head>
<style>


    @media print {
        @page {
            size: 89mm 36mm;
            margin-top: 3mm;
            margin-bottom: 3mm;
            margin-left: 3mm;
            margin-right: 3mm;
            margin-header: 0px;
            margin-footer: 0px;
        }

        body * {
            visibility: hidden;
        }
        #section-to-print, #section-to-print * {
            visibility: visible;
        }
        #section-to-print {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
    <script type="text/javascript" src="/bower_components/jquery/jquery.js"></script>
</head>
<body>
<div id="section-to-print" class="container">
    <div class="row personal-data">
        <div class="col-xs-12">
            <span>{{ "Order no."|t }}: <strong>{{ order.code }}</strong><input type="hidden" /> </span>
        </div>
        {% if order.DentistOrderData.getPatientNumber() is not null %}
            <div class="col-xs-12">
                <span>{{ "Patient number"|t }}: <strong>{{ order.DentistOrderData.getPatientNumber() }}</strong></span>
            </div>
        {% endif %}
        <div class="col-xs-12">
            <span><strong>{{ dentist.getName() }}</strong></span>
        </div>
        <div class="col-xs-12">
            <span><strong>{{ lab.getName() }}</strong></span>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        window.print();
        window.close();
    });

</script>
</body>
</html>