<html>
<head>
<style>
    @page {
        size: auto;
        margin-top: 3mm;
        margin-bottom: 3mm;
        margin-left: 3mm;
        margin-right: 3mm;
        margin-header: 0px;
        margin-footer: 0px;
    }
</style>
    <script type="text/javascript" src="/bower_components/jquery/jquery.js"></script>
</head>
<body>
<div class="container" style="font-size: 12px; width: 89mm; height: 36mm; margin: auto; padding: 10px; border: 1px solid #CCC;">
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