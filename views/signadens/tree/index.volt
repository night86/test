{% extends "layouts/main.volt" %}
{% block title %} {{ 'Lab'|t }} {% endblock %}
{% block content %}

    <h3>{{ "Products tree preview"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            {% if page.items|length > 0 %}
                <div id="product-list">
                    {% for product in page.items %}
                        <div class="col-md-4">
                            <div class="product-content">
                                <a href="{{ url('signadens/tree/index/' ~ product.getId()) }}">
                                    {% if product.image is not defined or product.image is null %}
                                        <div class="product-image"
                                             style="background: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');"></div>
                                        {#<img class="product-image img-responsive"#}
                                        {#src="http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar" alt="No photo"/>#}
                                    {% else %}
                                        <div class="product-image"
                                             style="background: url('{{ url(product.image) }}');"></div>
                                        {#<img class="product-image img-responsive" src="{{ url(product['image']) }}" alt="{{ product['name'] }}"/>#}
                                    {% endif %}
                                    <span class="productTreeName">{{ product.name }}</span>
                                </a>
                            </div>
                        </div>
                    {% endfor %}

                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="pagination">
                            {% if page.current != 1 %}
                                <li><a href="{{ url('signadens/tree/index/' ~ currentid) }}?page=1">1</a>
                                </li>{% endif %}
                            {% if page.before != 1 %}
                                <li>
                                <a href="{{ url('signadens/tree/index/' ~ currentid) }}?page={{ page.before }}">{{ page.before }}</a>
                                </li>{% endif %}
                            <li class="active"><a href="#">{{ page.current }}</a></li>
                            {% if page.next != page.last %}
                                <li>
                                <a href="{{ url('signadens/tree/index/' ~ currentid) }}?page={{ page.next }}">{{ page.next }}</a>
                                </li>{% endif %}
                            {% if page.last != page.current %}
                                <li>
                                <a href="{{ url('signadens/tree/index/' ~ currentid) }}?page={{ page.last }}">{{ page.last }}</a>
                                </li>{% endif %}
                        </ul>
                    </div>
                </div>
            {% endif %}
            {% if currentCategory.Recipes is defined and currentCategory.Recipes and currentCategory.Recipes|length > 0 %}
                <h3>{{ 'Products'|t }}</h3>
                <div id="product-list">
                    {% for recipe in currentCategory.Recipes %}

                        <div class="col-md-4">
                            <div class="product-content">
                                {#<a href="{{ url('dentist/order/showproduct/' ~ orderId ~ '/' ~ recipe.code) }}" class="addProduct">#}
                                <a href="#" class="addProduct">
                                    {% if recipe.image is null %}
                                        <div class="product-image"
                                             style="background: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');position: relative;height:350px;     background-position: center;
    background-repeat: no-repeat;
    background-size: contain;">
                                            {% if recipe.Organisation.logo is not null AND recipe.Organisation.connectedWithDentist() %}
                                                {% set logo = recipe.Organisation.logo %}
                                                <img class="supplier-logo"
                                                     src="{{ image('organisation', logo) }}"
                                                     alt="Supplier logo"/>
                                            {% endif %}
                                        </div>
                                    {% else %}
                                        <div class="product-image"
                                             style="background: url('{{ url(recipeImage ~ recipe.image) }}');position: relative;height:350px;     background-position: center;
                                                     background-repeat: no-repeat;
                                                     background-size: contain;">
                                            {% if recipe.Organisation.logo is not null AND recipe.Organisation.connectedWithDentist() %}
                                                {% set logo = recipe.Organisation.logo %}
                                                <img class="supplier-logo"
                                                     src="{{ image('organisation', logo) }}"
                                                     alt="Supplier logo"/>
                                            {% endif %}
                                        </div>
                                    {% endif %}
                                    <span class="productTreeName">{{ recipe.recipe_number }} - {{ recipe.name }}</span>
                                </a>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            {% endif %}
        </div>
    </div>


{% endblock %}