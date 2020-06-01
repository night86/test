{% for product in products %}
    <div class="col-md-12 single-item single-item-list">
        <div class="product-content pull-left" style="z-index: 9999;">
            {{ check_field("check_"~product['id'], "id":"check_"~product['id'], "class":"check-all", "data-id" : product['id']) }}
        </div>
        <div style="width: 64px; float: left; clear: both;">
            <img class="supplier-logo hidden" src="{#{ image('organisation', logo) }#}"
                 alt="{{ "Supplier logo"|t }}"/>
        </div>
        <div class="product-content">
            <div class="row">
                <div class="col-sm-4" style="margin-left: 25px;">
                    <span class="product-price">
                        <strong>{{ product['name'] }}</strong>{% if product['amount_include'] is defined and product['amount_include'] !== null %}, {{ "box of"|t }} {{ product['amount_include'] }}{% endif %}
                    </span>
                </div>
                <div class="col-sm-2">
                    {% if product['special_order'] > 0 %}
                        <img src="/public/images/products/special_product_icon_white.png" style="width: 50px; height: 50px; position: absolute; z-index: 99;"/>
                    {% endif %}
                    <div class="product-image" style="background-image: url({{ productImage(product['images']) }});"></div>
                </div>
                <div class="col-sm-6">
                    <div class="row actions-description">
                        <div class="col-sm-6">
                            <div class="product-numbers">
                                <p class="product-attribute">{{ "Code Extern"|t }}
                                    : {{ product['code'] }}</p>
                                <p class="product-attribute">{{ "Supplier"|t }}
                                    : <strong data-supplier="{{ product['supplier_id'] }}"  class="supplier-name">{#{ product['supplier_name'] }#}</strong></p>
                                <p class="product-attribute">{{ "Signa Code"|t }}
                                    : {{ product['signa_id'] }}</p>
                                <p class="product-attribute">{{ "Price"|t }}
                                    : &euro;{{ product['price'] }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="product-actions">
                                                        <span class="product-amount">
                                                                <input type="number" name="product_amount"
                                                                       {% if product['amount_min'] is defined and product['amount_min'] !== null %}min="{{ product['amount_min'] }}"
                                                                       value="{{ product['amount_min'] }}"
                                                                       {% else %}min="1" value="1"{% endif %}
                                                                        {% if product['amount'] is defined and product['amount'] !== null %}max="{{ product['amount'] }}"{% endif %}
                                                                ></span>
                                <a href="{{ url('lab/product/addcart/'~product['id']) }}" id="{{ 'product_'~product['id'] }}" data-id="{{ product['id'] }}"
                                   class="{% if product['special_order'] > 0 %}special-order {% endif %}add-cart btn btn-primary"><i
                                            class="pe-7s-cart"></i> {{ "Add to cart"|t }}</a>
                                <a href="{{ url('lab/product/addshortlist/'~product['id']) }}"
                                   class="add-shortlist btn btn-primary"
                                   data-amount="{% if product['amount_min'] is defined and product['amount_min'] !== null %}{{ product['amount_min'] }}{% else %}1{% endif %}">
                                    <i class="pe-7s-cart"></i> {{ "Add to shortlist"|t }}</a>
                                <span class="details btn btn-info"
                                      data-content="{{ partial("layouts/partial/productDetails", ['product': product]) }}"><i
                                            class="pe-7s-help1"></i></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
{% endfor %}