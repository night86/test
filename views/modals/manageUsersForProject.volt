<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                {{ content|t }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary {{ additionalClass }}" >{{ "Save"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {

        $('.save-users').click(function (e) {
            e.preventDefault();
            $(this).closest('.modal-content').find('form').submit();
        });

        $(".select2-users").select2({
            placeholder: 'Select users:',
            theme: "bootstrap",
            multiple: true,
            tokenSeparators: [',', ' '],
            //dropdownParent: $(this).parent(),
            tags: true,
            language: "nl",
            allowClear: true,
            ajax: {
                url: "/projects/manage",
                dataType: 'json',
                type: "GET",
                delay: 250,
                data: function (params) {
                    var queryParameters = {
//                        projectId: $('#manageUsersProjectId').val(),
                        q: params.term,
                        page: params.page
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            console.log(item);
                            return {
                                text: item.email,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
        $('.manageUsersBtn').click(function () {
            var projectID = $(this).data('projectid');
            $.ajax({
                type: 'GET',
                url: '/projects/manage/?projectId=' + projectID,
                dataType: 'json'
            }).then(function (data) {
                $.each(data, function (i,user) {
                    var $option = $('<option selected>'+user.email+'</option>').val(user.id)
                    $('.select2-users').append($option).trigger('change');
                })
            });
        });

    });
</script>