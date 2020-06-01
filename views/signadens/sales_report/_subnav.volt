<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SIGNADENS_PRODUCT_INDEX') %}
                    <li {{ controllername is 'product' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/product/') }}">{{ 'Recipes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_LEDGER_INDEX') %}
                    <li {{ controllername is 'ledger' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/ledger/') }}">{{ 'Ledger codes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_TARIFF_INDEX') %}
                    <li {{ controllername is 'tariff' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/tariff/') }}">{{ 'Tariff codes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_MAP_INDEX') %}
                    <li {{ controllername is 'map' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/map/') }}">{{ 'Map ledger and tariffs'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_TREE_INDEX') %}
                    <li {{ controllername is 'tree' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/tree/') }}">{{ "Product tree"|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_INDEX_INDEX') %}
                    <li {{ controllername is 'importcode' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/importcode/') }}">{{ "Import"|t }}</a>
                    </li>
                {% endif %}
                {#{% if currentUser.hasRole('ROLE_SIGNADENS_INDEX_INDEX') %}
                    <li {{ controllername is 'importcode' ? 'class="active"' : '' }}>
                        <a href="{{ url('/signadens/sales_report/') }}">{{ "Sales Report"|t }}</a>
                    </li>
                {% endif %}#}
            </ul>


        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>