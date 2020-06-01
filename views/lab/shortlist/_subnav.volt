<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">

                {% if currentUser.hasRole('ROLE_LAB_PRODUCT_INDEX') %}
                    <li {{ controllername is 'product'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/product/') }}">{{ 'Signadens database'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SHORTLIST_INDEX') %}
                <li {{ controllername is 'shortlist'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/lab/shortlist/') }}">{{ 'Your shortlist'|t }}</a>
                </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_CART_INDEX') %}
                    <li {{ controllername is 'cart'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/cart/') }}">{{ "Cart"|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_ORDER_INDEX') %}
                    <li {{ controllername is 'order'
                    and (actionname is 'index' or actionname is '')
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/order/') }}">{{ 'Orders in progress'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_ORDER_INDEX') %}
                    <li {{ controllername is 'order'
                    and actionname is 'history'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/order/history') }}">{{ 'Order history'|t }}</a>
                    </li>
                {% endif %}


{#
                {% if currentUser.hasRole('ROLE_LAB_COUNTLIST_INDEX') %}
                <li {{ controllername is 'countlist'
                ? 'class="active"' : '' }}>
                    <a href="{{ url('/lab/countlist/') }}">{{ 'Countlist'|t }}</a>
                </li>
                {% endif %}
#}

            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>