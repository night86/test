<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SIGNADENS_ORGANISATION_INDEX') %}
                    <li {{ controllername is 'organisation' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/organisation/') }}">{{ 'Organisations'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_USER_INDEX') %}
                    <li {{ controllername is 'user' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/user/') }}">{{ 'Users'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SIGNADENS_ROLE_INDEX') %}
                    <li {{ controllername is 'role' ? 'class="active"' : '' }}>
                        <a href="{{ url('signadens/role/') }}">{{ 'Roles'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>