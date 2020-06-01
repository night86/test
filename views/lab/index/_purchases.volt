<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_NEW') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingNewProduct">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#collapseNewProduct"
                       aria-expanded="true" aria-controls="collapseNewProduct">
                        {% if mproductsCount > 0 %}
                            <span class="badge">{{ mproductsCount }}</span>
                        {% endif %}
                        {{ "New product"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapseNewProduct" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="headingNewProduct">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {% if mproducts is not empty %}
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>{{ "Product Code"|t }}</th>
                                        <th>{{ "Product Name"|t }}</th>
                                        <th>{{ "Price"|t }}</th>
                                        <th>{{ "Name supplier"|t }}</th>
                                        {#<th>{{ "Actions"|t }}</th>#}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% for k,product in mproducts if k < 5 %}
                                        <tr>
                                            {% if product.details|isArray %}
                                                <td>{{ product.details['code'] }}</td>
                                                <td>{{ product.details['name'] }}</td>
                                                <td>{{ product.details['price'] }} {{ product.details['currency'] }}</td>
                                            {% else %}
                                                <td>{{ product.details.code }}</td>
                                                <td>{{ product.details.name }}</td>
                                                <td>{{ product.details.price }} {{ product.details.currency }}</td>
                                            {% endif %}
                                            <td>{{ product.supplierName }}</td>
                                            {#<td><a href="{{ url('lab/product/show/'~ product.productid ~ '/' ~ product._id) }}" class="btn btn-warning btn-sm"><i#}
                                                            {#class="pe-7s-glasses"></i> {{ "Show product"|t }}</a>#}
                                            {#</td>#}
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                                <a href="{{ url('lab/index/newProducts') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}

    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_ALERT') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingPriceChange">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse"
                       href="#collapsePriceChange" aria-expanded="false" aria-controls="collapsePriceChange">
                        {% if productsCount > 0 %}
                            <span class="badge">{{ productsCount }}</span>
                        {% endif %}
                        {{ "Alert price change"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapsePriceChange" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="headingPriceChange">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {% if products is not empty %}
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>{{ "Date"|t }}</th>
                                        <th>{{ "Product Code"|t }}</th>
                                        <th>{{ "Product Name"|t }}</th>
                                        <th>{{ "Old Price"|t }}</th>
                                        <th>{{ "New Price"|t }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% for k,product in products if k < 5 %}
                                        <tr>
                                            <td>{{ timetostrdt(product.start_date) }}</td>
                                            <td>{{ product.product_code }}</td>
                                            <td>{{ product.product_name }}</td>
                                            <td>{{ product.old_price }}</td>
                                            <td>{{ product.new_price }}</td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                                <a href="{{ url('lab/index/priceAlerts') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}
    {#<div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingAproveOrder">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse"
                   href="#collapseAproveOrder" aria-expanded="false" aria-controls="collapseAproveOrder">
                    {{ "Approve and send order"|t }}
                </a>
            </h4>
        </div>
        <div id="collapseAproveOrder" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="headingAproveOrder">
            <div class="panel-body">
                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3
                wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum
                eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla
                assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt
                sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus
                labore sustainable VHS.
            </div>
        </div>
    </div>#}

    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_STATUS') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingStatusOrder">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse"
                       href="#collapseStatusOrder" aria-expanded="false" aria-controls="collapseStatusOrder">
                        {% if status is not empty and status|length > 0 %}
                            <span class="badge">{{ status|length }}</span>
                        {% endif %}
                        {{ "Status change order"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapseStatusOrder" class="panel-collapse collapse" role="tabpanel"
                 aria-labelledby="headingStatusOrder">
                <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                {% if status is not empty %}
                                    <table class="table table-striped table-bordered" cellspacing="0"
                                           width="100%">
                                        <thead>
                                        <tr>
                                            <th>{{ "Order Number"|t }}</th>
                                            <th>{{ "Supplier name"|t }}</th>
                                            <th>{{ "Old Status"|t }}</th>
                                            <th>{{ "New Status"|t }}</th>
                                            <th>{{ "Actions"|t }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {% for k,stat in status if k < 5 %}
                                            <tr>
                                                <td>{{ stat.order_name }}</td>
                                                <td>{% if stat.order_supplier_name is defined %}{{ stat.order_supplier_name }}{% endif %}</td>
                                                <td>
                                                    {% if statusesNames[stat.order_oldstatus] is defined %}
                                                        {{ statusesNames[stat.order_oldstatus]|t }}
                                                    {% endif %}
                                                </td>
                                                <td>
                                                    {% if statusesNames[stat.order_status] is defined %}
                                                        {{ statusesNames[stat.order_status]|t }}
                                                    {% endif %}
                                                </td>
                                                <td>
                                                    <a href="{{ url('lab/order/orderdetails/' ~ stat.order_name) }}">{{ 'show more'|t }}</a>
                                                </td>
                                            </tr>
                                        {% endfor %}
                                        </tbody>
                                    </table>
                                    {#<a href="{{ url('lab/index/priceAlerts') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>#}
                                {% endif %}
                            </div>
                        </div>
                </div>
            </div>
        </div>
    {% endif %}

    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_NEW_IN_SHORTLIST') %}
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingNewShortlistOrder">
                <h4 class="panel-title">
                    <a {% if curretQuery['shortlist'] is not defined %}class="collapsed"{% endif %} role="button" data-toggle="collapse"
                       href="#collapseNewShortlistOrder" aria-expanded="false" aria-controls="collapseNewShortlistOrder">
                        {% if newShortlist is not empty and newShortlist|length > 0 %}
                            <span class="badge">{{ newShortlist|length }}</span>
                        {% endif %}
                        {{ "New products on shortlist"|t }}
                    </a>
                </h4>
            </div>
            <div id="collapseNewShortlistOrder" class="panel-collapse collapse {% if curretQuery['shortlist'] is defined %}in{% endif %}" role="tabpanel"
                 aria-labelledby="headingNewShortlistOrder">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {% if newShortlist is not empty %}
                                <table class="table table-striped table-bordered" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>{{ "Product code"|t }}</th>
                                        <th>{{ "Product name"|t }}</th>
                                        <th>{{ "Supplier"|t }}</th>
                                        <th>{{ "Price"|t }}</th>
                                        <th>{{ "Actions"|t }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% for k,newShortlistEl in newShortlist if k < 5 %}
                                        <tr>
                                            <td>
                                                {% if newShortlistEl.Product %}
                                                    {{ newShortlistEl.Product.code }}
                                                {% endif %}
                                            </td>
                                            <td>
                                                {% if newShortlistEl.Product %}
                                                    {{ newShortlistEl.Product.name }}
                                                {% endif %}
                                            </td>
                                            <td>
                                                {% if newShortlistEl.Product and newShortlistEl.Product.Organisation %}
                                                    {{ newShortlistEl.Product.Organisation.name }}
                                                {% endif %}
                                            </td>
                                            <td>
                                                {% if newShortlistEl.Product %}
                                                    {{ newShortlistEl.Product.getPrice() }}
                                                {% endif %}
                                            </td>
                                            <td>
                                                <a href="{{ url('lab/shortlist/markasviewed/'~ newShortlistEl.id) }}" class="btn btn-warning btn-sm">
                                                    <i class="pe-7s-glasses"></i> {{ "Mark as viewed"|t }}
                                                </a>
                                            </td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                                <a href="{{ url('lab/shortlist/') }}" class="btn btn-primary pull-right">{{ "Show all"|t }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}
</div>