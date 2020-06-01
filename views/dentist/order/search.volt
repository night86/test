{% extends "layouts/main.volt" %}
{% block title %} {{ "Order add new product"|t }} {% endblock %}
{% block content %}
    <div class="row">
        <div class="col-md-12">
            <h3>{{ 'Products'|t }}</h3>
            <h3><a href="{{ url("dentist/order/add/" ~ orderId) }}"><i class="pe-7s-back"></i></a></h3>
            {{ text_field('query', 'value': searchQuery, 'class': 'form-control width-100 margin-15 typeahead', 'id': 'search-field', 'placeholder': 'Search'|t, 'style': 'display:inline-block') }}
            <a href="{{ url('dentist/order/search') }}" class="btn btn-primary" id="search-products" data-order="{{ orderId }}" style="display: none;">{{ 'Search'|t }}</a>
            <a class="btn btn-warning" id="search-clear" style="display: inline-block">X</a>
            <div id="product-list" class="grid">
                {% for recipe in page.items %}

                    <div class="col-md-4">
                        <div class="product-content dentist-order" style="height: auto;">
                            <a href="{{ url('dentist/order/showproduct/' ~ orderId ~ '/' ~ recipe.code) }}" class="addProduct">
                                {% if not recipe.ParentRecipe or recipe.ParentRecipe.image is null %}
                                    <div class="product-image"
                                         style="background-image: url('http://placehold.it/600x350/ffffff?text=Geen+foto+beschikbaar');">
                                    </div>
                                {% else %}
                                    <div class="product-image" style="background-image: url('{{ url(recipeImage ~ recipe.ParentRecipe.image) }}');">
                                    </div>
                                {% endif %}
                                <span class="productTreeName">
                                    {% if recipe.customName %}
                                        {{ recipe.customName }}
                                    {% else %}
                                        {{ recipe.name }}
                                    {% endif %}
                                </span>
                            </a>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ul class="pagination">
                {% if page.current != 1 %}
                    <li><a href="{{ url('dentist/order/search/' ~ orderId) }}?page=1&query={{ searchQuery }}">1</a></li>{% endif %}
                {% if page.before != 1 %}
                    <li><a href="{{ url('dentist/order/search/' ~ orderId) }}?page={{ page.before }}&query={{ searchQuery }}">{{ page.before }}</a>
                    </li>{% endif %}
                <li class="active"><a href="#">{{ page.current }}</a></li>
                {% if page.next != page.last %}
                    <li><a href="{{ url('dentist/order/search/' ~ orderId) }}?page={{ page.next }}&query={{ searchQuery }}">{{ page.next }}</a>
                    </li>{% endif %}
                {% if page.last != page.current %}
                    <li><a href="{{ url('dentist/order/search/' ~ orderId) }}?page={{ page.last }}&query={{ searchQuery }}">{{ page.last }}</a>
                    </li>{% endif %}
            </ul>
        </div>
    </div>


{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            products.init('{{ url('dentist/order/ajaxnames') }}');

            $('#search-clear').on('click', function(){
                $('#search-field').val(null);
                location.href = '/dentist/order/add/{{ orderId }}';
            });
        });
    </script>
{% endblock %}

{% block styles %}
    {{ super() }}
    <style>
        ul.typeahead {
            top: 102px !important;
            left: 30px !important;
        }
    </style>
{% endblock %}