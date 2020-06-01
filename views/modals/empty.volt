<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>

            <div class="modal-reply" style="padding: 15px;">
                {% if currentUser.hasRole('ROLE_SIGNADENS_INDEX_INDEX') %}
                <label id="removal-label">{{'Confirm product removal?'|t}}</label>
                    {{ check_field('product_removal', 'checked': 'checked') }}<br />
                    <label id="reply-label">{{'Reply'|t}}</label>
                    <textarea rows="4" name="reply" id="reply-content" class="form-control" style="width: 100%"></textarea>
                    <div class="text-right" style="margin-top: 15px;">
                        <button type="button" class="btn btn-default" data-replyurl="{{ url('notification/ajaxreply') }}" id="reply">{{ "Send reply"|t }}</button>
                    </div>
                {% endif %}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="button-confirm">{{ "Save"|t }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ "Close"|t }}</button>
            </div>
            <script>
                $(function(){

                    $('#product_removal').click(function(){
                        if($('#product_removal').is(':checked') == false){
                            $("#reply").show();
                            $("#reply-content").show();
                            $("#reply-label").show();
                            $("#button-confirm").hide();
                        }
                        else {
                            $("#reply").hide();
                            $("#reply-content").hide();
                            $("#reply-label").hide();
                            $("#button-confirm").show();
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>