<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY') %}
                    <li {{ actionname is 'indexcategory' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/manage/indexcategory') }}">{{ 'Manage categories'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_MANAGE_TREECATEGORY') %}
                    <li {{ actionname is 'treecategory' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/manage/treecategory') }}">{{ 'Tree categories'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_INVOICE_INDEX') %}
                    <li {{ actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/invoice/') }}">{{ 'Invoices'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_HELPDESK') %}
                    <li {{ controllername is 'helpdesk'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/helpdesk/') }}">{{ 'Helpdesk'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>