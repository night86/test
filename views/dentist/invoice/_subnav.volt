<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_DENTIST_ORGANISATION_EDIT') %}
                    <li {{ controllername is 'user' and actionname is 'organisation' ? 'class="active"' : '' }}>
                        <a href="{{ url('dentist/user/organisation/' ~ currentUser.getOrganisationId()) }}">{{ 'Organisation'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_DENTIST_USER_INDEX') %}
                    <li {{ controllername is 'user' and actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('dentist/user/' ~ currentUser.getOrganisationId()) }}">{{ 'Users'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_DENTIST_INVOICE_INDEX') %}
                    <li {{ controllername is 'invoice' and actionname is '' ? 'class="active"' : '' }}>
                        <a href="{{ url('dentist/invoice/') }}">{{ 'Invoices'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>