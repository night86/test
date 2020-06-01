<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SUPPLIER_ORGANISATION_INDEX') %}
                    <li {{ controllername is 'user' and actionname is 'organisation' ? 'class="active"' : '' }}>
                        <a href="{{ url('supplier/user/organisation/' ~ organisation.getId()) }}">{{ 'Organisation'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>