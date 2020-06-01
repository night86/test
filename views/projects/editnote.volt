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
                        <input id="note_id" type="hidden" name="note_id" value="{{ note.id }}"/>
                        <legend> </legend>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status">{{ "Title"|t }}</label>
                                {{ text_field('title', 'class': 'form-control', 'required': 'required', 'value': note.title) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">{{ "Description"|t }}</label>
                                {{ text_area('content', 'class': 'form-control', 'required': 'required', 'value': note.content) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <label>{{ 'Select a file on your computer to upload'|t }}</label><br>
                    <span class="btn btn-success fileinput-button"><i class="pe-7s-plus"></i><span>{{ "Add file"|t }}</span>
                    <input id="fileupload" type="file" name="file" data-url="{{ url('/projects/uploadnotefile/' ~ projectId  ~'/'~ note.id) }}">

                    </span>
                    <div id="uploader"></div>
                    <div id="progress"><div class="bar" style="width: 0%;"></div></div>
                </div>

                <div class="col-md-12">
                    {% for oneFile in note.Files %}
                        <div class="form-group">
                            <a href="{{ url('projects/downloadnotefile/'~ projectId ~ '/' ~ oneFile.getId()) }}" class="btn btn-default btn-sm"><i class="pe-7s-cloud-download"></i> {{ oneFile.getNameOriginal() }}</a>
                            <a href="{{ url('projects/deletenotefile/'~ projectId ~ '/' ~ note.getId() ~ '/' ~ oneFile.getId()) }}" class="btn btn-danger btn-sm delete"><i class="pe-7s-trash"></i> {{'Delete'|t}}</a>
                        </div>
                    {% endfor %}
                </div>

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