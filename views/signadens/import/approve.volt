{% extends "layouts/main.volt" %}
{% block title %} Signadens {% endblock %}
{% block content %}

    <h3>{{ "Import"|t }}</h3>

    <div class="row">
        <div class="col-md-12">
            {{ form('signadens/import/approve/'~id, 'method': 'post') }}
                <h5>{{ "Import by"|t }} {{ userFullName }} {{ "on"|t }} {{ effectiveFrom|dttonl }}</h5>
                <table class="table table-striped">
                    <thead>
                        <th>{{ check_field('selectedAll', 'class': 'select-all-products') }}</th>
                        <th>{{ "Product Code"|t }}</th>
                        <th>{{ "Product Name"|t }}</th>
                        {% if type == 'update' %}
                            <th>{{ "New Product"|t }}</th>
                            <th>{{ "Old Product"|t }}</th>
                        {% else %}
                            <th>{{ "Product price"|t }}</th>
                            <th>{{ "Main product category"|t }}</th>
                            <th>{{ "Sub category"|t }}</th>
                            <th>{{ "Subsub category"|t }}</th>
                            {#<th>{{ "Package unit"|t }}</th>#}
                            <th>{{ "Delivery time"|t }}</th>
                            <th>{{ "Material"|t }}</th>
                            <th>{{ "Manufacturer"|t }}</th>
                            {#<th>{{ "Barcode supplier"|t }}</th>#}
                            {#<th>{{ "Key word for product searching"|t }}</th>#}
                        {% endif %}
                        <th>{{ "Status"|t }}</th>
                    </thead>
                    <tbody>
                        {% for index, product in products %}
                            <tr>
                                <td>{{ check_field('selectedProducts[]', 'value': product.id, 'class': 'group-checkbox') }}</td>
                                <td>{{ product.code }}</td>
                                <td>{{ product.name }}</td>
                                {% if type == 'update' %}
                                    {% set row = product.compareOldNew() %}
                                    {% if row['old'] is defined %}
                                        <td>
                                            {% for fname, fvalue in row['new'] %}
                                                {% if fvalue|isArray and fvalue['change'] is defined and fvalue['change'] is true %}
                                                    <span class="product-list-value"><strong>{{ fname|t }}
                                                            :</strong> {{ fvalue['value'] }}</span>
                                                {% endif %}
                                            {% endfor %}
                                        </td>
                                        <td>
                                            {% for fname, fvalue in row['new'] %}
                                                {% set fvalue = row['old'][fname] %}
                                                {% if fvalue|isArray and fvalue['change'] is defined and fvalue['change'] is true %}
                                                    <span class="product-list-value"><strong>{{ fname|t }}
                                                            :</strong> {{ fvalue['value'] }}</span>
                                                {% endif %}
                                            {% endfor %}
                                        </td>
                                    {% else %}
                                        <td>
                                            <p>{{ "Create new product"|t }}</p>
                                        </td>
                                        <td></td>
                                    {% endif %}
                                {% else %}
                                    <td>{{ product.price }}</td>
                                    <td>
                                        {% if product.MainCategory %}
                                            {{ product.MainCategory.name }}
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% if product.SubCategory %}
                                            {{ product.SubCategory.name }}
                                        {% endif %}
                                    </td>
                                    <td>
                                        {% if product.SubSubCategory %}
                                            {{ product.SubSubCategory.name }}
                                        {% endif %}
                                    </td>
                                    {#<td>{% if product.package_unit is defined %}{{ product.package_unit }}{% endif %}</td>#}
                                    <td>{{ product.delivery_time }}</td>
                                    <td>{{ product.material }}</td>
                                    <td>{{ product.manufacturer }}</td>

                                    {#<td>{{ product.barcode_supplier }}</td>#}
                                    {#<td>{{ product.key_words }}</td>#}
                                {% endif %}
                                    <td>
                                        {% if product.approval_status is 'error_category' %}
                                            <a class="btn btn-warning btn-sm add-category" data-product="{{ product.id }}"><i class="pe-7s-attention"></i> {{ "Add missing category"|t }}</a>
                                        {% elseif product.approval_status is 'error_range' or product.approval_status is 'error_out' %}
                                            <a href="/signadens/manage/tariffcoderanges" class="btn btn-warning btn-sm" target="_blank"><i class="pe-7s-attention"></i> {{ "Add missing tariff code range"|t }}</a>
                                        {% endif %}
                                    </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
                <button type="submit" class="btn btn-danger pull-left" name="decline"><i class="pe-7s-close"></i> {{ "Decline all none selected"|t }}</button>
                <button type="submit" class="btn btn-primary pull-right" name="approve">{{ "Approve selected"|t }} <i class="pe-7s-angle-right"></i></button>
            {{ end_form() }}
        </div>
    </div>

    <div id="categoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ "Add missing category"|t }}</h4>
                </div>
                <div class="modal-body">
                    <div id="singleFieldForm">
                        <div class="form-group">
                            <label for="name">{{ "Product category"|t }}:</label>
                        </div>
                        <div class="form-group">
                            <select id="product_category" class="select2-input">
                                <option></option>
                                {% for pc in productCategories %}
                                    <option value="{{ pc['id'] }}">{% if pc['cat_parent_name'] %}{{ pc['cat_parent_name'] }} - {% endif %}{% if pc['sub_parent_name'] %}{{ pc['sub_parent_name'] }} - {% endif %}{{ pc['name'] }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="confirmButton" type="button" class="btn btn-primary confirm-button">{{ "Confirm"|t }} </button>
                    <button type="button" class="btn btn-default cancel-button" data-dismiss="modal">{{ "Cancel"|t }}</button>
                </div>
            </div>
        </div>
    </div>

{% endblock %}
{% block scripts %}
    {{ super() }}
    <script>
        $(function(){

            $('.add-category').on('click', function(){

                $('#confirmButton').attr('data-product', $(this).attr('data-product'));
                $('#categoryModal').modal('show');
            });

            $('#confirmButton').on('click', function(){

                var product_id = $(this).attr('data-product');
                var product_category_id = $('#product_category').val();

                if(product_category_id != ''){

                    $.ajax({
                        method: 'POST',
                        url: '/signadens/import/ajaxmissingcategory/',
                        data: {
                            product_category_id: product_category_id, product_id: product_id
                        },
                        success: function(data){
                            var obj = $.parseJSON(data);
                            if(obj.status != "error"){
                                setTimeout(function () {
                                    toastr.success(obj.msg);
                                    setTimeout(function () {
                                        location.href = '/signadens/import/approve/{{ id }}';
                                    }, 1000);
                                }, 1000);
                            }
                            else {
                                setTimeout(function () {
                                    toastr.error(obj.msg);
                                }, 1000);
                            }
                        }
                    });
                    $('#categoryModal').modal('hide');
                }
                else {
                    toastr.error("{{ "Please fill in missing fields."|t }}");
                }
            });
            $('#selectedAll').on('change', function(){
                if($(this).prop('checked'))
                {
                    $('.datatable-checkbox').prop('checked', true);
                }else{
                    $('.datatable-checkbox').prop('checked', false);
                }
            });
        });
    </script>
{% endblock %}
