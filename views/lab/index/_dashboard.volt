<div class="col-md-12 col-sm-12">
    {% if
        currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_NEW')
        or currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_ALERT')
        or currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_STATUS')
        or currentUser.hasRole('ROLE_LAB_DASHBOARD_PURCHASES_NEW_IN_SHORTLIST')
    %}
        <h4>{{ "Purchases"|t }}</h4>
        {{ partial("lab/index/_purchases") }}
    {% endif %}

    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_SALES_NEW_ORDER') %}
        <h4>{{ "Sales"|t }}</h4>
        {{ partial("lab/index/_sales") }}
    {% endif %}

    {% if currentUser.hasRole('ROLE_LAB_DASHBOARD_MANAGEMENT_USERS') %}
        <h4>{{ "Application management"|t }}</h4>
        {{ partial("lab/index/_management") }}
    {% endif %}
</div>