{% extends "layouts/main.volt" %}
{% block title %} {{ 'Product management'|t }} {% endblock %}
{% block content %}

    <h3>{{ 'Product management'|t }}</h3>


    <div class="row">
        <div class="col-md-12">
            <table width="100%" id="supplierProductsList" class="table table-striped">
                <thead>
                    <th></th>
                    <th>{{ "Signadens code"|t }}</th>
                    <th>{{ "Product name"|t }}</th>
                    <th>{{ "Price"|t }}</th>
                    {#<th class="sortbydate">{{ "Effective from"|t }}</th>#}
                    <th>{{ "Action"|t }}</th>
                </thead>

                {#<tbody>#}
                {#{% for product in products %}#}
                        {#<tr>#}
                            {#<td>#}
                                {#{% if product['image'] is null %}#}
                                    {#<img src="http://placehold.it/300x100/ffffff?text=Geen+foto+beschikbaar" alt="">#}
                                {#{% else %}#}
                                    {#<img src="{{ url(product['image']) }}" alt="" style="max-width:300px; max-height: 120px;">#}
                                {#{% endif %}#}
                            {#</td>#}
                            {#<td>{{ product['id'] }}</td>#}
                            {#<td>{{ product['name'] }}</td>#}

                            {#<td>{{ product['price'] }}</td>#}
                            {#<td>#}
                                {#<a href="{{ url('/supplier/productlist/edit/') ~ product['id'] }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i>  {{ "Edit"|t }}</a>#}
                            {#</td>#}
                        {#</tr>#}
                {#{% endfor %}#}
                {#</tbody>#}
            </table>
        </div>
    </div>

    {{ partial("modals/confirmGeneral", ['id': 'confirm-modal', 'title': "Delete"|t, 'content': "Are you sure you want to delete?"|t]) }}

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            products.init("{{ url('/supplier/productlist/listajax') }}");
            products.initDataTables();

            $('.delete-product').on('click', function(e){
                e.preventDefault();
                $href = $(this).attr('href');
                var confirmModal = $('#confirm-modal');
                confirmModal.modal('show');
                console.log($href);

                $('.confirm-button').on('click', function(){
                    confirmModal.modal('hide');
                    window.location = $href;
                });
            });
        });
    </script>
{% endblock %}
