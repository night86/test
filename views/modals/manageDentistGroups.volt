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

        $('.save-groups').click(function (e) {
            e.preventDefault();
            form = $(this).closest('.modal-content').find('form');
            form.find('#input_product_id').val({{ code }});
            form.submit();
        });

        $(".select2-groups").select2({
            placeholder: '{{ "Select groups"|t }}:',
            theme: "bootstrap",
            multiple: true,
            tokenSeparators: [',', ' '],
            tags: true,
            //dropdownParent: $(this).parent(),
            language: "nl",
            allowClear: true,
            ajax: {
                url: "/signadens/product/groupmanage",
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
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
//

    });
</script>