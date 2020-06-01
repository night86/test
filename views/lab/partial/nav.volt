<ul class="nav navbar-nav">
    {% if currentUser.hasRole('ROLE_LAB_INDEX_INDEX') %}
        <li {{ controllername is 'index'
            ? 'class="active"' : '' }}>
            <a href="{{ url('lab/index/') }}">{{ 'Dashboard'|t }}</a>
        </li>
    {% endif %}
    {# old: or currentUser.hasRole('ROLE_LAB_COUNTLIST_INDEX') #}
    {% if currentUser.hasRole('ROLE_LAB_SHORTLIST_INDEX')
        or currentUser.hasRole('ROLE_LAB_ORDER_INDEX')
        or currentUser.hasRole('ROLE_LAB_PRODUCT_INDEX')
        or currentUser.hasRole('ROLE_LAB_CART_INDEX')
    %}
        <li {{ controllername is 'product'
            or controllername is 'shortlist'
            or controllername is 'order'
            or controllername is 'countlist'
            or controllername is 'cart'
            ? 'class="active"' : '' }}>
            {% if currentUser.hasRole('ROLE_LAB_PRODUCT_INDEX') %}
                <a href="{{ url('lab/product/') }}">{{ 'Buying'|t }}</a>
            {% else %}
                <a href="{{ url('lab/shortlist/') }}">{{ 'Buying'|t }}</a>
            {% endif %}
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_LAB_SALESORDER_INDEX')

    %}
        <li {{ controllername is 'sales_order'
            ? 'class="active"' : '' }}>
            <a href="{{ url('lab/sales_order/') }}">{{ 'Sales'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_LAB_USER_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESCLIENT_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESRECIPE_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESIMPORT_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESLEDGER_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESLEDGER_MAP')
        or currentUser.hasRole('ROLE_LAB_SALESTARIFF_INDEX')
        or currentUser.hasRole('ROLE_LAB_SALESTARIFF_MAP')
    %}
        <li {{ controllername is 'user'
            or controllername is 'sales_client'
            or controllername is 'sales_ledger'
            or controllername is 'sales_tariff'
            or controllername is 'sales_recipe'
            or controllername is 'sales_import'
            ? 'class="active"' : '' }}>
            <a href="{{ url('lab/user/') }}">{{ 'Users'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasFiles() %}
        <li {{ controllername is 'file'
        ? 'class="active"' : '' }}>
            <a href="{{ url('lab/file/') }}">{{ 'Files'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_PROJECTS_INDEX') %}
        <li {{ controllername is 'projects'
        ? 'class="active"' : '' }}>
            <a href="{{ url('projects/') }}">{{ 'Projects'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_LAB_AVG_INDEX') %}
        <li {{ controllername is 'avg'
        ? 'class="active"' : '' }}>
            <a href="{{ url('lab/avg/') }}">{{ 'Quality control'|t }}</a>
        </li>
    {% endif %}
</ul>