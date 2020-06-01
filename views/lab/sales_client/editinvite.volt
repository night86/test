{% extends "layouts/main.volt" %}
{% block title %} {{ "Edit client"|t }} {% endblock %}
{% block content %}

    <fieldset class="form-group">
        <legend>{{ "Edit"|t }}</legend>
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="client_number">{{ "Client number"|t }}&nbsp;</label>
                            {{ text_field('client_number', 'class': 'form-control', 'value': invite.getClientNumber()) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">&nbsp;</label>
                            <button id="confirm-button" type="submit" class="btn btn-primary btn-block"><i class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-lg-offset-0 col-md-6 col-md-offset-6">

            </div>
        </div>
    </fieldset>
{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>
        $(function(){
            $('#confirm-button').on('click', function(){
                $.ajax({
                    method: 'POST',
                    url: '/lab/sales_client/editinvite/{{ id }}',
                    data: { client_number: $('#client_number').val() },
                    success: function(data){
                        var obj = $.parseJSON(data);
                        if(obj.status != "error"){
                            setTimeout(function () {
                                toastr.success(obj.msg);
                                setTimeout(function () {
                                    location.href = '/lab/sales_client/pending/';
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
            });
        });
    </script>
{% endblock %}