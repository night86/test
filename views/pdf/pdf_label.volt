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
</head>
<body>
<div class="container" style="font-size: 12px;">
    <div class="row personal-data">
        <div class="col-xs-12">
            <span>{{ "Order no."|t }}: <strong>{{ order.code }}</strong></span>
        </div>
        {% if order.DentistOrderData.getPatientNumber() is not null %}
            <div class="col-xs-12">
                <span>{{ "Patient number"|t }}: <strong>{{ order.DentistOrderData.getPatientNumber() }}</strong></span>
            </div>
        {% endif %}
        {#{% if order.DentistOrderBsn.getBsn() is not null %}#}
            {#<div class="col-xs-12">#}
                {#<span>{{ "BSN"|t }}: <strong>{{ order.DentistOrderBsn.getBsnSecured() }}</strong></span>#}
            {#</div>#}
        {#{% endif %}#}
    </div>
    <div class="row">
        {#<div class="col-xs-5 address">#}
        {#<span>{{ lab.name }}</span><br>#}
        {#<span>{{ lab.address }}</span><br>#}
        {#</div>#}
        <div class="col-xs-5 address text-right">
            <span>{{ dentist.name }}</span><br>
            <span>{{ lab.name }}</span>
        </div>
    </div>
</div>
</body>
</html>