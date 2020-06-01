{% for product in products %}
    <div class="col-md-4 single-item single-item-grid">
        <div class="product-content">
            <div class="product-image-area">
                {% if product['special_order'] > 0 %}
                    <img src="/public/images/products/special_product_icon_white.png"
                         style="width: 50px; height: 50px; position: absolute; z-index: 99;"/>
                {% endif %}
                <div class="product-image" style="background-image: url({{ productImage(product['images']) }});">
                    <img class="supplier-logo hidden" src="{#{ image('organisation', logo) }#}"
                         alt="{{ "Supplier logo"|t }}"/>
                </div>
            </div>

            <div class="product-numbers database-grid">
                <p class="product-attribute">{{ "Code Extern"|t }}: {{ product['code'] }}</p>
                <p class="product-attribute">{{ "Signa Code"|t }}: {{ product['signa_id'] }}</p>
                <p class="product-attribute">{{ "Price"|t }}: &euro;{{ product['price'] }}</p>
            </div>

            <div class="product-actions">
                <span class="product-price">
                    <strong>{{ product['name'] }}</strong>{% if product['amount_include'] is defined and product['amount_include'] !== null %}, {{ "box of"|t }} {{ product['amount_include'] }}{% endif %}
                </span>
                <span class="product-price">
                    <strong data-supplier="{{ product['supplier_id'] }}"  class="supplier-name">{#{ product['supplier_name'] }#}</strong>
                </span>
                <span class="product-amount">
                    <input type="number" name="product_amount"
                        {% if product['amount_min'] is defined and product['amount_min'] !== null %}
                            min="{{ product['amount_min'] }}" value="{{ product['amount_min'] }}"
                        {% else %}
                            min="1" value="1"
                        {% endif %}
                        {% if product['amount'] is defined and product['amount'] !== null %}
                            max="{{ product['amount'] }}"
                        {% endif %}
                    />
                </span>

                <a href="{{ url('lab/product/addcart/'~product['id']) }}" id="{{ 'product_'~product['id'] }}" data-id="{{ product['id'] }}"
                   class="{% if product['special_order'] > 0 %}special-order {% else %} add-cart {% endif %} btn btn-primary"><i class="pe-7s-cart"></i> {{ "Add to cart"|t }}</a>
                {% if currentUser.hasRole('ROLE_LAB_SHORTLIST_EDIT') %}
                    <a href="#" class="btn btn-success edit"
                       data-url="{{ url('/lab/shortlist/ajaxproductamount/' ~ product['id']) }}"
                       data-save="{{ url('/lab/shortlist/ajaxsaveamount/' ~ product['id']) }}"
                       data-shortlist="{{ url('/lab/shortlist/delete/' ~ product['id']) }}"
                    >
                        <i class="pe-7s-edit"></i> {{ "Edit"|t }}
                    </a>
                {% endif %}
                <span class="details btn btn-info" data-content="{{ partial("layouts/partial/productDetails", ['product': product]) }}">
                    <i class="pe-7s-help1"></i>
                </span>
            </div>
        </div>
    </div>
{% endfor %}