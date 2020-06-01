<ul class="nav navbar-nav">
    {% if currentUser.hasRole('ROLE_DENTIST_INDEX_INDEX') %}
    <li {{ controllername is 'index'
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/index/') }}">{{ 'Dashboard'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_ORDER_INDEX') %}
    <li {{ controllername is 'order'
        and actionname is ''
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/order/') }}">{{ 'Dentist_orders'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_ORDER_INDEX') %}
        <li {{ controllername is 'order'
        and actionname is 'inprogress'
        ? 'class="active"' : '' }}>
            <a href="{{ url('dentist/order/inprogress') }}">{{ 'Dentist orders in progress'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_ORDER_HISTORY') %}
    <li {{ controllername is 'order'
        and actionname is 'history'
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/order/history') }}">{{ 'Orders history'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_USER_INDEX') %}
        <li {{ controllername is 'user'
        ? 'class="active"' : '' }}>
            <a href="{{ url('dentist/user/') }}">{{ 'Users'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_GROUPCONTRACT_INDEX') %}
    <li {{ controllername is 'group_contract'
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/group_contract/') }}">{{ 'Contracts'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_GROUPINVOICE_INDEX') %}
    <li {{ controllername is 'group_invoice'
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/group_invoice/') }}">{{ 'Invoices'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_GROUPDENTIST_INDEX') %}
    <li {{ controllername is 'group_dentist'
        ? 'class="active"' : '' }}>
        <a href="{{ url('dentist/group_dentist/') }}">{{ 'Manage Dentists'|t }}</a>
    </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_DENTIST_FILE_INDEX') %}
        <li {{ controllername is 'file'
        ? 'class="active"' : '' }}>
            <a href="{{ url('dentist/file/') }}">{{ 'Files'|t }}</a>
        </li>
    {% endif %}
    {% if currentUser.hasRole('ROLE_PROJECTS_INDEX') %}
        <li {{ controllername is 'projects'
        ? 'class="active"' : '' }}>
            <a href="{{ url('projects/') }}">{{ 'Projects'|t }}</a>
        </li>
    {% endif %}
</ul>