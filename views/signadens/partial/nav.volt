<ul class="nav navbar-nav">

    {% if currentUser.hasRole('ROLE_SIGNADENS_INDEX_INDEX') %}
        <li {{ controllername is 'index'
            ? 'class="active"' : '' }}>
            <a href="{{ url('signadens/index/') }}">{{ 'Dashboard'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SIGNADENS_IMPORT_INDEX') %}
        <li {{ controllername is 'import'
            ? 'class="active"' : '' }}>
            <a href="{{ url('signadens/import/') }}">{{ 'Approve imports'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SIGNADENS_USER_INDEX') %}
        <li {{ controllername is 'user'
            or controllername is 'role'
            or controllername is 'organisation'
            ? 'class="active"' : '' }}>
            <a href="{{ url('signadens/user/') }}">{{ 'Users'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SIGNADENS_PRODUCT_INDEX') %}
        <li {{ controllername is 'product'
            or controllername is 'ledger'
            or controllername is 'tariff'
            or controllername is 'map'
            or controllername is 'tree'
            or controllername is 'importcode'
            or controllername is 'salesreport'
            ? 'class="active"' : '' }}>
            <a href="{{ url('signadens/product/') }}">{{ 'Product management'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY') %}
        <li {{ controllername is 'manage'
            or controllername is 'invoice'
            ? 'class="active"' : '' }}>
            <a href="{{ url('signadens/manage/indexcategory') }}">{{ 'Application management'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_PROJECTS_INDEX') %}
        <li {{ controllername is 'projects'
        ? 'class="active"' : '' }}>
            <a href="{{ url('projects/') }}">{{ 'Projects'|t }}</a>
        </li>
    {% endif %}

    {#
    <li {#{{ controllername is 'index'
        ? 'class="active"' : '' }}>
        <a href="{{ url('signadens/helpdesk/') }}">{{ 'Helpdesk'|t }}</a>
    </li>
    #}
</ul>