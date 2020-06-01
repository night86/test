{% extends "layouts/main.volt" %}
{% block title %} {{ "Order add new product"|t }} {% endblock %}
{% block content %}
    {% if noCategories is not defined %}
        <div class="row">
            <div class="col-md-12">
                {% if page.items|length > 0 %}
                <div class="row">
                    <div class="col-md-6">
                        <h3>{{ 'Order'|t }}</h3>
                        <h4>{{ 'Click on one of categories to go to the next step'|t }}</h4>
                    </div>
                    <div class="col-md-6">
                        {% if labLogo is defined %}
                            <img class="img-responsive pull-right" src="{{ image('organisation', labLogo) }}" alt="Logo"
                                 style="max-width: 340px; max-height: 170px; margin: 10px 0 0 0;"/>
                        {% endif %}
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="search-fields">
                                <h3>
                                    {% if breadcrumbs[(breadcrumbs|length - 2)] is defined %}
                                        <a href="{{ url('dentist/order/add/'~orderId~'/'~breadcrumbs[(breadcrumbs|length - 2)]['id']) }}">
                                            <i class="pe-7s-back"></i>
                                        </a>
                                    {% elseif breadcrumbs|length is 0 %}
                                        <a href="{{ url('dentist/order/edit/'~orderId) }}">
                                            <i class="pe-7s-back"></i>
                                        </a>
                                    {% else %}
                                        <a href="{{ url('dentist/order/add/' ~ orderId) }}">
                                            <i class="pe-7s-back"></i>
                                        </a>
                                    {% endif %}
                                </h3>
                                {{ text_field('query', 'class': 'form-control width-100 margin-15 typeahead',
                                'id': 'search-field', 'placeholder': 'Zoeken', 'style': 'display:inline-block') }}
                                <a href="{{ url('dentist/order/search') }}" class="btn btn-primary" id="search-products" data-order="{{ orderId }}" style="display: none;">{{ 'Search'|t }}</a>
                            </div>
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('dentist/order/add/' ~ orderId) }}">{{ "New order"|t }}</a></li>
                                    {% if breadcrumbs is defined %}
                                        {% for breadcrumb in breadcrumbs %}
                                            {% if loop.last is true %}
                                                <li class="active"><a href="{{ url('dentist/order/add/'~orderId~'/'~breadcrumb['id']) }}">{{ breadcrumb['name'] }}</a></li>
                                            {% else %}
                                                <li><a href="{{ url('dentist/order/add/'~orderId~'/'~breadcrumb['id']) }}">{{ breadcrumb['name'] }}</a></li>
                                            {% endif %}
                                        {% endfor %}
                                    {% endif %}
                                </ol>
                        </div>
                    </div>

                <div id="product-list" class="grid">
                    {% for product in page.items %}
                        <div class="col-md-4">
                            <div class="product-content dentist-order">
                                <a href="{{ url('dentist/order/add/' ~ orderId ~ '/' ~ product.getId()) }}{% if labId is not null %}/{{ labId }}{% endif %}">
                                    {% if product.image is null %}
                                        <div class="product-image"
                                             style="background-image: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');position: relative;height:350px;">
                                            {#{% if labLogo is defined %}#}
                                            {#<img class="supplier-logo" src="{{ image('organisation', labLogo) }}" alt="Logo"/>#}
                                            {#{% endif %}#}
                                        </div>
                                        {#<img class="product-image img-responsive"#}
                                        {#src="http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar" alt="No photo"/>#}
                                    {% else %}
                                        <div class="product-image"
                                             style="background-image: url('{{ url(categoryImage ~ product.image) }}');position: relative;height:350px;">
                                            {#{% if labLogo is defined %}#}
                                            {#<img class="supplier-logo" src="{{ image('organisation', labLogo) }}" alt="Logo"/>#}
                                            {#{% endif %}#}
                                        </div>
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
                                <li><a href="{{ url('dentist/order/add/' ~ orderId ~ '/' ~ currentid) }}?page=1">1</a>
                                </li>{% endif %}
                            {% if page.before != 1 %}
                                <li>
                                <a href="{{ url('dentist/order/add/' ~ orderId ~ '/' ~ currentid) }}?page={{ page.before }}">{{ page.before }}</a>
                                </li>{% endif %}
                            <li class="active"><a href="#">{{ page.current }}</a></li>
                            {% if page.next != page.last %}
                                <li>
                                <a href="{{ url('dentist/order/add/' ~ orderId ~ '/' ~ currentid) }}?page={{ page.next }}">{{ page.next }}</a>
                                </li>{% endif %}
                            {% if page.last != page.current %}
                                <li>
                                <a href="{{ url('dentist/order/add/' ~ orderId ~ '/' ~ currentid) }}?page={{ page.last }}">{{ page.last }}</a>
                                </li>{% endif %}
                        </ul>
                    </div>
                </div>
                {% endif %}
                {% if categoryRecipes|length > 0 or page.items|length is 0 %}
                    <div class="row">
                        <div class="col-md-6">
                            <h3>{{ 'Products'|t }}</h3>
                        </div>
                        <div class="col-md-6">
                            {% if labLogo is defined %}
                                <img class="img-responsive pull-right" src="{{ image('organisation', labLogo) }}"
                                     alt="Logo" style="max-width: 340px; max-height: 170px; margin: 10px 0px 15px 0px;"/>
                            {% endif %}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>{{ 'Make additional choices in the filters or choose the recipe you want to order.'|t }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-lg-1">
                                <div class="search-fields step2">
                                    <h3><a href="javascript:history.back()"><i class="pe-7s-back"></i></a></h3>
                                </div>
                            </div>
                            <div class="col-lg-11">
                                <ol class="breadcrumb">
                                    <li><a href="{{ url('dentist/order/create') }}">{{ "New order"|t }}</a></li>
                                    {% if breadcrumbs is defined %}
                                        {% for breadcrumb in breadcrumbs %}
                                            {% if loop.last is true %}
                                                <li class="active"><a href="{{ url('dentist/order/add/'~orderId~'/'~breadcrumb['id']) }}">{{ breadcrumb['name'] }}</a></li>
                                            {% else %}
                                                <li><a href="{{ url('dentist/order/add/'~orderId~'/'~breadcrumb['id']) }}">{{ breadcrumb['name'] }}</a></li>
                                            {% endif %}
                                        {% endfor %}
                                    {% endif %}
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="filtersForm" method="get">
                                {% for setting in recipeSettings %}
                                    <div class="form-group recipe-filter">
                                        <div><label>{{ setting.name }}</label></div>
                                        {% for option in setting.Options %}
                                            <div so="{{ setting.id }}-{{ option.id }}" data-setting="{{ setting.id }}" data-option="{{ option.id }}" class="recipe-filter-option">
                                                <div class="recipe-filter-option-image"></div>
                                                <div class="recipe-filter-option-name">{{ option.name }}</div>
                                                <div class="recipe-filter-option-checked">
                                                    {% if recipeSettingsSelected[setting.id] is defined and recipeSettingsSelected[setting.id] is option.id %}
                                                        <i class="fa fa-check-square" aria-hidden="true"></i>
                                                    {% else %}
                                                        <i class="fa fa-square" aria-hidden="true"></i>
                                                    {% endif %}
                                                    <input {% if recipeSettingsSelected[setting.id] is defined and recipeSettingsSelected[setting.id] is option.id %}checked="checked"{% endif %} type="radio" value="{{ option.id }}" name="setting[{{ setting.id }}]" />
                                                </div>
                                            </div>
                                        {% endfor %}
                                    </div>
                                {% endfor %}
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr />
                            <p>{{ 'Available recipes within this branch, with current filtering'|t }}</p>
                        </div>
                    </div>
                    <div id="product-list" class="grid">
                        {% for recipe in basicRecipe %}
                        {% if recipe.is_basic == 1 %}
                        <div class="col-md-4">
                            <div class="product-content dentist-order">
                                <a href="{{ url('dentist/order/showproduct/' ~ orderId ~ '/' ~ recipe.code) }}" class="addProduct">
                                    <div class="product-image" style="background-image: url('{{ url(recipeImage ~ recipe.ParentRecipe.image) }}');"></div>
                                    <span class="productTreeName">{{ recipe.ParentRecipe.recipe_number }} - {{ recipe.name }}</span>
                                </a>
                            </div>
                        </div>
                        {% endif %}
                        {% endfor %}
                        {% for activeRecipe in categoryRecipes %}
                            {% if activeRecipe.is_basic == 1 %}
                            {% continue %}
                            {% else %}
                                <div class="col-md-4">
                                    <div class="product-settings">
                                        {% for recipeSetting in activeRecipe.ParentRecipe.RecipeSettings %}
                                            <div
                                                    class="setting"
                                                    data-setting="{{ recipeSetting.setting_id }}"
                                                    data-option="{{ recipeSetting.option_id }}"
                                            ></div>
                                        {% endfor %}
                                    </div>
                                    <div class="product-content dentist-order">
                                        <a href="{{ url('dentist/order/showproduct/' ~ orderId ~ '/' ~ activeRecipe.code) }}"
                                           class="addProduct">
                                            {% if activeRecipe.ParentRecipe.image is null %}
                                                <div class="product-image" style="background-image: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');"></div>
                                            {% else %}
                                                <div class="product-image" style="background-image: url('{{ url(recipeImage ~ activeRecipe.ParentRecipe.image) }}');"></div>
                                            {% endif %}
                                            <span class="productTreeName">{{ activeRecipe.ParentRecipe.recipe_number }} - {{ activeRecipe.ParentRecipe.name }}</span>
                                        </a>
                                    </div>
                                </div>
                            {% endif %}
                        {% endfor %}
                    </div>
                {% endif %}
            </div>
        </div>
    {% else %}
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info text-center" role="alert">
                    {{ "There is no product avilable for you yet"|t }}
                </div>
            </div>
        </div>
    {% endif %}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            {% if currentUser.Organisation.getOrganisationTypeId() == 4 %}
            products.init('{{ url('dentist/order/ajaxnames/'~ orderId) }}');
            {% else %}
            products.init('{{ url('dentist/order/ajaxnames') }}');
            {% endif %}
        });
    </script>
{% endblock %}