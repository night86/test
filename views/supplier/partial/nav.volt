<ul class="nav navbar-nav">

    {% if currentUser.hasRole('ROLE_SUPPLIER_INDEX_INDEX') %}
        <li {{ controllername is 'index'
            ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/index/') }}">{{ 'Dashboard'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SUPPLIER_IMPORT_INDEX') %}
        <li {{ controllername is 'import'
            ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/import/') }}">{{ 'Import'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SUPPLIER_ORDER_INDEX') %}
        <li {{ controllername is 'order'
            and actionname is ''
            ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/order/') }}">{{ 'Orders'|t }}</a>
        </li>
        <li {{ controllername is 'order'
        and actionname is 'history'
        ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/order/history') }}">{{ 'Orders history'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SUPPLIER_USER_INDEX') %}
        <li {{ controllername is 'user'
            ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/user/') }}">{{ 'Users'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SUPPLIER_IMPORTLOG_INDEX') %}
        <li {{ controllername is 'importlog'
        ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/importlog/') }}">{{ 'Import log'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_PROJECTS_INDEX') %}
        <li {{ controllername is 'projects'
        ? 'class="active"' : '' }}>
            <a href="{{ url('projects/') }}">{{ 'Projects'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SUPPLIER_PRODUCTSLIST_INDEX') %}
        <li {{ controllername is 'productlist'
        ? 'class="active"' : '' }}>
            <a href="{{ url('supplier/productlist/labview/') }}">{{ 'Product management'|t }}</a>
        </li>
    {% endif %}
</ul>