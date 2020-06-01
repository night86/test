{% extends "layouts/main.volt" %}
{% block title %} {{ "Product categories"|t }} {% endblock %}
{% block content %}

    {% macro render_tree(tree_array) %}
        <ul>
            {% for category in tree_array %}
                <li>
                    <i class="pe-7s-folder"></i> {{ category['name'] }}
                    <div class="tree-actions">
                        <a href="#">{{ "Add"|t }}</a>
                        <a href="#">{{ "Edit"|t }}</a>
                        <a href="#">{{ "Delete"|t }}</a>
                    </div>
                    {% if category['children'] is defined %}
                        {{ render_tree(category['children']) }}
                    {% endif %}
                </li>
            {% endfor %}
        </ul>
    {% endmacro %}

    <p class="pull-right"><a id="addNewStep" href="javascript:;" class="btn btn-primary"><i class="pe-7s-plus"></i> {{ "Add new"|t }}</a></p>
    <h3>{{ "Product categories"|t }}</h3>

    <div id="categoryTree" class="well">
        {{ rendertree }}
    </div>

    <div class="modal fade" id="addTreeCategoryModal" tabindex="-1" role="dialog"
         aria-labelledby="addTreeCategoryModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTreeCategoryModalLabel">{{ "Add new category"|t }}</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="text" placeholder="{{ "New category"|t }}" class="form-control" name="name">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Close"|t }}</button>
                    <button id="sendAjax" type="button" class="btn btn-primary">{{ "Save"|t }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addTreeCategoryProductModal" role="dialog"
         aria-labelledby="addTreeCategoryModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ 'Close'|t }}"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTreeCategoryModalLabel">{{ "Add product"|t }}</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <div class="col-md-12">
                                <select class="form-control select2-input new-product" name="product">
                                    {% for product in products %}
                                        <option value="{{ product.id }}">{{ product.code }} - {{ product.name }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ 'Close'|t }}</button>
                    <button id="saveProduct" type="button" class="btn btn-primary">{{ 'Add'|t }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="confirmModalLabel">{{ "Confirm"|t }}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ 'Are you sure you want to remove?'|t }} </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ 'Close'|t }}</button>
                    <button id="confirmDelete" type="button" class="btn btn-primary">{{ 'Confirm'|t }}</button>
                </div>
            </div>
        </div>
    </div>

{% endblock %}

{% block scripts %}
{{ super() }}
<script type="text/javascript">

    var parentid = '';
    var deleteid = '';
    var deleteproductid = '';
    var currentid = '';


    var sendSort = {
        init: function () {
            var list = document.getElementsByClassName("sort-list");
            for (var i = 0; i < list.length; i++) {
                var tlist = document.getElementsByClassName("list-" + (i + 1))[0];

                var startArr = [];
                var endArr = [];
                Sortable.create(tlist, {
                    group: "localStorage-example-" + (i + 1),
                    onStart: function (/**Event*/evt) {
                        evt.oldIndex;  // element index within parent
                        $(evt.from).children('li').each(function () {
                            var startIndex = $(this).data('id').toString();
                            startArr.push(startIndex);
                        });
                    },
                    // Element dragging ended
                    onEnd: function (/**Event*/evt) {
                        evt.oldIndex;  // element's old index within parent
                        evt.newIndex;  // element's new index within parent
                        $(evt.from).children('li').each(function () {
                            var endIndex = $(this).data('id').toString();
                            endArr.push(endIndex);
                        });
                        sendSort.sendAjax(startArr, endArr);
                    }
                });
            }
        },
        sendAjax: function (startA, endA) {
            $.ajax({
                method: "get",
                url: "{{ url("signadens/manage/sorttreecategory") }}",
                data: {start: startA, end: endA, 'categoryType': 'productCategory'}
            })
                .done(function (msg) {

                });
        }
    }

    $('#addNewStep').on('click', function (e) {
        e.preventDefault();
        parentid = '';
        $('#addTreeCategoryModal').modal('show')
    })

    $('#sendAjax').on('click', function (e) {
        e.preventDefault();
        sendSaveAjax(parentid);
    });

    $('#saveProduct').on('click', function (e) {
        e.preventDefault();
        sendSaveProduct(currentid);
    });

    $(document).on('click', '.add-product', function (e) {
        e.preventDefault();
        currentid = $(this).attr('data-id');
        $('#addTreeCategoryProductModal').modal('show')
    });
    $(document).on('click', '.add-step', function (e) {
        e.preventDefault();
        parentid = $(this).attr('data-parendid');
        $('#addTreeCategoryModal').modal('show')
    });
    $(document).on('click', '.delete-step', function (e) {
        e.preventDefault();
        deleteid = $(this).attr('data-parendid');
        $('#confirmModal').modal('show')
    });
    $(document).on('click', '.delete-product', function (e) {
        e.preventDefault();
        deleteid = $(this).data('parendid');
        deleteproductid = $(this).data('productid');
        $('#confirmModal').modal('show')
    });

    $('#confirmDelete').on('click', function (e) {
        e.preventDefault();
        sendDeleteAjax(deleteid, deleteproductid);
    });

    function sendSaveAjax(id) {
        id = (id == undefined) ? '' : id;
        var form = $('#addTreeCategoryModal').find('form').serialize() + '&id=' + id;
        $.ajax({
            method: "POST",
            url: "/signadens/manage/addcategory",
            dataType: 'json',
            data: form
        }).done(function (msg) {
            $('#categoryTree').html(msg.html);
            if (msg.status == 'success') {
                sendSort.init();
                toastr.success(msg.message);
            } else {
                toastr.error(msg.message);
            }

            $('#addTreeCategoryModal').modal('hide').find('input').val('');
        });
    }
    function sendDeleteAjax(id) {
        $.ajax({
            method: "POST",
            url: "/signadens/manage/deletecategory",
            dataType: 'json',
            data: {'id': id, 'productId': deleteproductid}
        }).done(function (msg) {
            if (msg.type == 'success') {
                sendSort.init();
                toastr.success(msg.content);
                $('.category_'+id).fadeOut();
            } else {
                toastr.error(msg.content);
            }
            $('#confirmModal').modal('hide');
        });
        deleteproductid = '';
    }
    function sendSaveProduct(id) {
        id = (id == undefined) ? '' : id;


        var exist = false;
        $('.category_' + id).find('.delete-product').each(function () {
            if ($('#addTreeCategoryProductModal').find('.new-product').eq(0).val() == $(this).data('productid')) {
                exist = true;
            }
        });

        if (exist) {
            toastr.error('{{ 'Product exist in this node'|t }}');
            $('#addTreeCategoryProductModal').modal('hide').find('input').val('');
        } else {

            var form = $('#addTreeCategoryProductModal').find('form').serialize() + '&id=' + id;
            $.ajax({
                method: "POST",
                url: "/signadens/manage/addtreecategoryproduct",
                dataType: 'json',
                data: form
            }).done(function (msg) {
                $('#categoryTree').html(msg.html);
                if (msg.status == 'success') {
                    sendSort.init();
                    toastr.success(msg.message);
                } else {
                    toastr.error(msg.message);
                }

                $('#addTreeCategoryProductModal').modal('hide').find('input').val('');
            });
        }
    }


    $(document).ready(function(){
        sendSort.init();
    });


</script>
{% endblock %}