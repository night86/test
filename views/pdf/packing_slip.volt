<div class="container">
    <div class="row">
        <div class="col-xs-5 address">
            <span>{{ lab.name }}</span><br>
            <span>{{ lab.address }}</span><br>
            <span>{{ lab.zipcode }}</span><br>
            <span>{{ lab.city }}</span><br>
        </div>
        <div class="col-xs-5 address text-right">
            <span>{{ dentist.name }}</span><br>
            <span>{{ dentist.address }}</span><br>
            <span>{{ dentist.zipcode }}</span><br>
            <span>{{ dentist.city }}</span><br>
        </div>
    </div>
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
</div>


