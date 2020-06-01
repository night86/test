{% extends "layouts/main.volt" %}
{% block title %} {{ 'Notes'|t }} {% endblock %}

{% block content %}
    <h3>
        {{ "New note"|t }}
    </h3>

    <div class="row">
        <div class="col-lg-12">
            <fieldset class="form-group">
                {{ form('projects/addnote/' ~ projectId, 'method': 'post') }}
                <div class="row">
                    <div class="col-md-12">
                        <input id="project_id" type="hidden" name="project_id" value="{{ projectId }}" />
                        <input id="note_id" type="hidden" name="note_id" />
                        <legend> </legend>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status">{{ "Title"|t }}</label>
                                {{ text_field('title', 'class': 'form-control', 'required': 'required') }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{ "Description"|t }}</label>
                                {{ text_area('content', 'class': 'form-control', 'required': 'required') }}
                            </div>
                        </div>
                    </div>
                </div>

                {#<div class="col-md-12">#}
                    {#<label>{{ 'Select a file on your computer to upload'|t }}</label>#}
                    {#<input id="fileupload" multiple type="file" name="files[]" data-url=" {{ url('/projects/uploadnotefile/' ~ projectId) }}">#}
                    {#<div id="uploader"></div>#}
                    {#<div id="progress"><div class="bar" style="width: 0%;"></div></div>#}
                {#</div>#}

                <div class="col-md-12">
                    <br /><br />
                    <button type="submit" class="btn btn-primary pull-right uploaderinit"><i
                                class="pe-7s-diskette"></i> {{ "Save"|t }}</button>
                </div>
                {{ end_form() }}
            </fieldset>
        </div>
    </div>

{% endblock %}

{% block scripts %}
    {{ super() }}
    <script>

    </script>
{% endblock %}