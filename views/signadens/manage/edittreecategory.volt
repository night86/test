{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit tree category: "|t }} {% endblock %}
{% block content %}


    <h3><a href="{{ url("signadens/manage/treecategory") }}"><i class="pe-7s-back"></i></a> {{ "Edit tree category"|t }}: {{ category.name }}</h3>

    <fieldset class="form-group">
        {#<legend>Edit category</legend>#}
        <form method="post" enctype="multipart/form-data">

            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">{{'Name'|t}}</label>
                    <input type="text" name="name" value="{{ category.name }}" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">{{'Image'|t}}</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">&nbsp; </label>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right"><i class="pe-7s-diskette"></i> {{'Save'|t}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {% if image is defined %}
                <div class="col-md-3">
                    <div class="form-group">
                        <img src="{{ image }}" width="300" />
                    </div>
                </div>
            {% endif %}
        </form>

    </fieldset>

    <div class="modal fade" id="addTreeCategoryModal" tabindex="-1" role="dialog"
         aria-labelledby="addTreeCategoryModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTreeCategoryModalLabel">{{ "Add step"|t }}</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="text" placeholder="New step" class="form-control" name="name">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{'Close'|t}}</button>
                    <button id="sendAjax" type="button" class="btn btn-primary">{{'Save'|t}}</button>
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

        $('#addNewStep').on('click', function (e) {
            e.preventDefault();
            parentid = '';
            $('#addTreeCategoryModal').modal('show')
        })

        $('#sendAjax').on('click', function (e) {
            e.preventDefault();
            sendSaveAjax(parentid);
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
        })

        $('#confirmDelete').on('click', function (e) {
            e.preventDefault();
            sendDeleteAjax(deleteid);
        });

        function sendSaveAjax(id) {
            id = (id == undefined) ? '' : id;
            var form = $('#addTreeCategoryModal').find('form').serialize() + '&id=' + id;
            $.ajax({
                method: "POST",
                url: "/signadens/manage/addtreecategory",
                dataType: 'json',
                data: form
            }).done(function (msg) {
                $('#categoryTree').html(msg.html);
                if (msg.status == 'success') {
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
                url: "/signadens/manage/deletetreecategory",
                dataType: 'json',
                data: {'id': id}
            }).done(function (msg) {
                $('#categoryTree').html(msg.html);
                if (msg.status == 'success') {
                    toastr.success(msg.message);
                } else {
                    toastr.error(msg.message);
                }
                $('#confirmModal').modal('hide');
            });
        }
    </script>
{% endblock %}