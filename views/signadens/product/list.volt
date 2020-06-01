{% extends "layouts/main.volt" %}
{% block title %} {{ "Product list"|t }} {% endblock %}
{% block content %}

    <h3>{{ "Product list"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            <table id="signaProductsList" class="table table-striped">
                <thead>
                <th>{{ "ID"|t }}</th>
                <th>{{ "Code"|t }}</th>
                <th>{{ "Product name"|t }}</th>
                <th>{{ "Supplier name"|t }}</th>
                <th>{{ "Price"|t }}</th>
                {#<th class="sortbydate">{{ "Effective from"|t }}</th>#}
                <th>{{ "Action"|t }}</th>
                </thead>
                {#<tbody>#}
                    {#{% for product in products %}#}
                        {#{% if product.organisationName %}#}
                        {#<tr>#}
                            {#<td>{{ product.id }}</td>#}
                            {#<td>{{ product.code }}</td>#}
                            {#<td>{{ product.name }}</td>#}
                            {#<td>{{ product.organisationName }}</td>#}
                            {#<td>{{ product.price }}</td>#}
                            {#<td>#}
                                {#<a href="{{ url('/signadens/product/listedit/') ~ product.id }}" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i>  {{ "Edit"|t }}</a>#}
                                {#<a href="{{ url('/signadens/product/listdelete/') ~ product.id }}" class="delete-product btn btn-danger btn-sm"><i class="pe-7s-trash"></i>  {{ "Delete"|t }}</a>#}
                            {#</td>#}
                        {#</tr>#}
                        {#{% endif %}#}
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

            products.init("{{ url('/signadens/product/listajax') }}");
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