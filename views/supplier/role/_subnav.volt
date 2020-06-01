<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_SUPPLIER_USER_INDEX') %}
                    <li><a href="{{ url('supplier/user/') }}">{{ 'Users'|t }}</a></li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_SUPPLIER_ROLE_INDEX') %}
                    <li><a href="{{ url('supplier/role/') }}">{{ 'Roles'|t }}</a></li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>