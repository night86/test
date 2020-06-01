<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_LAB_ORGANISATION_EDIT') %}
                    <li {{ controllername is 'user' and actionname is 'organisation' ? 'class="active"' : '' }}>
                        <a href="{{ url('lab/user/organisation/' ~ organisation.getId()) }}">{{ 'Organisation'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_USER_INDEX') %}
                    <li {{ controllername is 'user' and actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('lab/user/' ~ organisation.getId()) }}">{{ 'Users'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESCLIENT_INDEX') %}
                    <li {{ controllername is 'sales_client'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_client/') }}">{{ 'Clients'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESRECIPE_INDEX') %}
                    <li {{ controllername is 'sales_recipe'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_recipe/') }}">{{ 'Recipes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESIMPORT_INDEX') %}
                    <li {{ controllername is 'sales_import'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_import/') }}">{{ 'Import'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESLEDGER_INDEX') %}
                    <li {{ controllername is 'sales_ledger'
                    and actionname is ''
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_ledger/') }}">{{ 'Manage ledger codes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESLEDGER_MAP') %}
                    <li {{ controllername is 'sales_ledger'
                    and actionname is 'map'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_ledger/map') }}">{{ 'Map ledger codes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESTARIFF_INDEX') %}
                    <li {{ controllername is 'sales_tariff'
                    and actionname is ''
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_tariff/') }}">{{ 'Manage tariff codes'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESTARIFF_MAP') %}
                    <li {{ controllername is 'sales_tariff'
                    and actionname is 'mappingandmargins'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_tariff/mappingandmargins') }}">{{ 'Mapping and margins'|t }}</a>
                    </li>
                {% endif %}
                {#{% if currentUser.hasRole('ROLE_LAB_SALESTARIFF_MAP') %}#}
                {#<li {{ controllername is 'sales_tariff'#}
                {#and actionname is 'map'#}
                {#? 'class="active"' : '' }}>#}
                {#<a href="{{ url('/lab/sales_tariff/map') }}">{{ 'Map tariff codes'|t }}</a>#}
                {#</li>#}
                {#{% endif %}#}
                {% if currentUser.hasRole('ROLE_LAB_USER_INDEX') %}
                    <li {{ controllername is 'user' and actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('lab/sales_report/') }}">{{ 'Sales report'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESRECIPE_EDIT') %}
                    <li {{ controllername is 'sales_recipe' and actionname is 'productiontime' ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_recipe/productiontime') }}">{{ 'Production time'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>