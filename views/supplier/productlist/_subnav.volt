<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SUPPLIER_PRODUCTSLIST_INDEX') %}
                    <li {{ controllername is 'productlist' and actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('/supplier/productlist/') }}">{{ 'Products list'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SUPPLIER_PRODUCTSLIST_INDEX') %}
                    <li {{ controllername is 'productlist' and actionname is 'labview' ? 'class="active"' : '' }}>
                        <a href="{{ url('/supplier/productlist/labview/') }}">{{ 'Lab view'|t }}</a>
                    </li>
                {% endif %}
            </ul>


        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>