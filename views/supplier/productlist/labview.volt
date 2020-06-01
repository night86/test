{% extends "layouts/main.volt" %}
{% block title %} {{ 'Lab view'|t }} {% endblock %}
{% block sidebarcontent %}
    <div class="categories toLoaderFull">
        {% include "lab/product/index/_productsFilters.volt" %}
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
                {#{% include "lab/product/index/_productsPagination.volt" %}#}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="product-list">
                    <div class="col-xs-11">
                        <div id="select-all" class="btn btn-primary pull-left" style="margin-right: 50px;">{{ 'Select all'|t }}</div>
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

                        <div id="show-selected" style="clear:both;">
                            <a id="short-selected" class="btn btn-primary" style="margin-top: -5px; margin-bottom: 10px;">
                                <i class="pe-7s-cart" style="font-size: 17px; margin-left: 0px; margin-right: 10px; opacity: 1;"></i>
                                {{ "Add selected products to shortlist"|t }}
                            </a>
                        </div>
                    </div>
                    <div id="display-products-switch" class="col-xs-1">
                        <i class="fa fa-th" aria-hidden="true"></i>
                        <i id="list_type" class="fa fa-th-list" aria-hidden="true"></i>
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
                {#{% include "lab/product/index/_productsPagination.volt" %}#}
            </div>
        </div>
    </div>
    <div class="supplierViewId" data-id="{{ currentUser.Organisation.getId() }}" />

    {#   Section with modals    #}

    {{ partial("modals/addSpecialOrder", ['id': 'special-order', 'title': 'Special order'|t, 'additionalClass': 'save-special-order','content': 'This product will be specially ordered for you. You can not return this. Are you sure you want to order this product?'|t ]) }}
    {{ partial("modals/confirmCart", ['idVariant': '', 'title': 'Order product for project'|t, 'content': 'Is this a general stock or for a specific project? If you enter a project number it will be saved to this product for tho order'|t]) }}
    {{ partial("modals/confirmCart", ['idVariant': '-already', 'title': 'Product already on order list'|t, 'content': 'This product is already on the order list and is waiting to be ordered by our company purcharser. Do you still want to add this on the order list for a specif project?'|t]) }}
    {{ partial("modals/alert", ['id': 'exist-shortlist', 'title': 'Error'|t, 'content': 'Product is already in the shortlist.'|t]) }}
    {{ partial("modals/alert", ['id': 'added-shortlist', 'title': 'Success'|t, 'content': 'Product has been added to the shortlist.'|t]) }}
    {{ partial("modals/alert", ['id': 'exist-cart', 'title': 'Success'|t, 'content': 'Product has been added to the orderlist.'|t]) }}
    {{ partial("modals/alert", ['id': 'added-cart', 'title': 'Success'|t, 'content': 'Product has been added to the cart.'|t]) }}
    {{ partial("modals/alert", ['id': 'product-detail-modal', 'title': 'Product details'|t, 'content': '']) }}
    {#   End section with modals    #}

{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(function () {
            products.init('{{ url('lab/product/ajaxnamessimple') }}');
            products.productDetails();
            productsSwitcher.init();
            if($(".check-all").is(":checked")){

                $("#show-selected").show();
            }
            else {
                $("#show-selected").hide();
            }

            $(".check-all").on("click", function(){
                if($(".check-all").is(":checked")){

                    $("#show-selected").show();
                }
                else {
                    $("#show-selected").hide();
                }
            });

            $("#select-all").on("click", function(){
                if($(".check-all").is(":checked")){

                    $("#show-selected").show();
                }
                else {
                    $("#show-selected").hide();
                }
            });

            $("#display-products-switch").on('click', function(){
                if($("#list_type").hasClass("active")){
                    $("#select-all").show();
                }
                else {
                    $("#select-all").hide();
                }
            });

            if($("#list_type").hasClass("active")){
                $("#select-all").show();
            }
            else {
                $("#select-all").hide();
            }
        });
    </script>
{% endblock %}