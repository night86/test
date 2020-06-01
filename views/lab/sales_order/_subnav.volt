<nav class="navbar navbar-default navbar-signa-sub">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                {% if currentUser.hasRole('ROLE_LAB_SALESORDER_INDEX') %}
                    <li {{ controllername is 'sales_order'  and actionname is 'incoming'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_order/incoming') }}">{{ 'Incoming orders'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESORDER_INDEX') %}
                    <li {{ controllername is 'sales_order'  and actionname is ''
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_order/') }}">{{ 'sales_orders_in_progress'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESORDER_INDEX') %}
                    <li {{ controllername is 'sales_order'  and actionname is 'history'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_order/history') }}">{{ 'Orders history'|t }}</a>
                    </li>
                {% endif %}
                {% if currentUser.hasRole('ROLE_LAB_SALESORDER_INDEX') %}
                    <li {{ controllername is 'sales_order'  and actionname is 'all'
                    ? 'class="active"' : '' }}>
                        <a href="{{ url('/lab/sales_order/all') }}">{{ 'All orders'|t }}</a>
                    </li>
                {% endif %}
            </ul>

        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>