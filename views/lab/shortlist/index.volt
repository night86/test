{% extends "layouts/main.volt" %}
{% block title %} {{ 'Your shortlist'|t }} {% endblock %}
{% block sidebarcontent %}
    <div class="categories toLoaderFull">
        {% include "lab/shortlist/index/_productsFilters.volt" %}
    </div>
{% endblock %}
{% block content %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="loader">
                    <div class="loader-inner box1"></div>
                    <div class="loader-inner box2"></div>
                    <div class="loader-inner box3"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="toLoader">
        <div class="row">
            <div class="col-md-12">
                <div class="pagination-area"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="product-list">
                    <div class="col-xs-9">
                        <div class="search-fields">
                            {% set placeholder='Search products'|t %}
                            {{ text_field('query', 'value':session.get('products-filters')['query'], 'class': 'form-control width-100 typeahead',
                                'id': 'search-field', 'placeholder':'Search products'|t , 'style': 'display:inline-block') }}
                            <a href="javascript:;" class="btn btn-primary" id="search-products"
                               style="display: inline-block">{{ 'Search'|t }}</a>
                            <a href="javascript:;" class="btn btn-warning" id="search-clear"
                               style="display: inline-block">{{ 'X'|t }}</a>
                        </div>

                        <div id="change-list-limit">
                            <div class="form-group pull-left" style="display: inline-block;">
                                <label for="page-limit-change" class="col-sm-1 control-label" style="margin-top: 8px; min-width: 100px;">{{ "per page"|t }}</label>
                                <select id="page-limit-change" name="page-limit-change" class="form-control" style="display: inline-block; width: 100px;" data-type="product">
                                    <option {% if session.get('products-filters')['limit'] is 6 %} selected="selected" {% endif %} value="6">6</option>
                                    <option {% if session.get('products-filters')['limit'] is 24 %} selected="selected" {% endif %} value="24">24</option>
                                    <option {% if session.get('products-filters')['limit'] is 48 %} selected="selected" {% endif %} value="48">48</option>
                                    <option {% if session.get('products-filters')['limit'] is 96 %} selected="selected" {% endif %} value="96">96</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="display-products-switch" class="col-xs-3">
                        {% if currentUser.hasRole('ROLE_ADMIN') %}<a href="#" class="btn btn-primary" data-url="{{ url('lab/shortlist/ajaxmargin') }}" id="setMargin" data-toggle="tooltip" data-placement="bottom" title="{{ "If you don't select any product, margin will be set for all products"|t }}">{{ "Change margin price"|t }}</a>{% endif %}
                        <i class="fa fa-th" aria-hidden="true"></i>
                        <i class="fa fa-th-list" aria-hidden="true"></i>
                    </div>

                    <div class="products-area">
                        <div class="products-area-grid">
                            {#{% include "lab/product/index/_productsGrid.volt" %}#}
                        </div>
                        <div class="products-area-list">
                            {#{% include "lab/product/index/_productsList.volt" %}#}
                        </div>
                    </div>
                </div>
                <div class="pagination-area"></div>
            </div>
        </div>
    </div>

    {#   Section with modals    #}

    {{ partial("modals/addSpecialOrder", ['id': 'special-order', 'title': 'Special order'|t, 'additionalClass': 'save-special-order','content': 'This product will be specially ordered for you. You can not return this. Are you sure you want to order this product?'|t ]) }}
    {{ partial("modals/confirmCart", ['idVariant': '', 'title': 'Order product for project'|t, 'content': 'Is this a general stock or for a specific project? If you enter a project number it will be saved to this product for tho order'|t]) }}
    {{ partial("modals/confirmCart", ['idVariant': '-already', 'title': 'Product already on order list'|t, 'content': 'This product is already on the order list and is waiting to be ordered by our company purcharser. Do you still want to add this on the order list for a specif project?'|t]) }}
    {{ partial("modals/alert", ['id': 'exist-cart', 'title': 'Success'|t, 'content': 'Product quantity has been added.'|t]) }}
    {{ partial("modals/alert", ['id': 'added-cart', 'title': 'Success'|t, 'content': 'Product has been added to the cart.'|t]) }}
    {{ partial("modals/alert", ['id': 'edited-modal', 'title': 'Success'|t, 'content': 'Product has been edited.'|t]) }}
    {{ partial("modals/alert", ['id': 'product-detail-modal', 'title': 'Product details'|t, 'content': '']) }}
    {{ partial("modals/confirm", ['id': 'edit-product', 'title': 'Edit product'|t, 'content': editContent, 'additionalClass': 'save-data', 'skiptranslation': true]) }}
    {{ partial("modals/confirm", ['id': 'margin-modal', 'title': 'Change margin price'|t, 'content': marginContent, 'additionalClass': 'save-margin-data', 'skiptranslation': true]) }}

    {#   End section with modals    #}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            products.init('{{ url('lab/shortlist/ajaxnamessimple') }}');
            products.productDetails();
            productsSwitcher.init();

            /*shortlist.marginPrice();
            $('[data-toggle="tooltip"]').tooltip();*/
        });
    </script>
{% endblock %}