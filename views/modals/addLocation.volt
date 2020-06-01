<div id="{{ id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ title|t }}</h4>
            </div>
            <div class="modal-body">
                <form id="singleFieldForm">
                    <div class="form-group">
                        <label for="locationName">{{ "Location name"|t }}:</label>
                        <input id="locationName" name="location[name]" class="form-control isModal" type="text" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="locationAddress">{{ "Address"|t }}:</label>
                        <input id="locationAddress" name="location[address]" class="form-control isModal" type="text" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="locationZipcode">{{ "Postal code"|t }}:</label>
                        <input id="locationZipcode" name="location[zipcode]" class="form-control isModal" type="text" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="locationCity">{{ "City"|t }}:</label>
                        <input id="locationCity" name="location[city]" class="form-control isModal" type="text" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="locationCountry">{{ "Country"|t }}:</label>
                        <select id="locationCountry" name="location[country_id]" class="form-control isModal" tabindex="13" required="required">
                            <option></option>
                            {% for country in countryList %}
                                <option value="{{ country.id }}">{{ country.name }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="locationTelephone">{{ "Telephone number"|t }}:</label>
                        <input id="locationTelephone" name="location[telephone]" class="form-control isModal" type="text" required="required" />
                    </div>
                    {% if type is not defined %}
                    <div class="form-group">
                        <label for="locationClientNumber">{{ "Client number"|t }}:</label>
                        <input id="locationClientNumber" name="location[client_number]" class="form-control isModal" type="text" required="required" />
                    </div>
                    {% endif %}
                </form>
            </div>
            <div class="modal-footer">
                <button id="confirmAddLocation" type="button" class="btn btn-primary">{% if confirmButton is defined %}{{ confirmButton|t }}{% else %}{{ "Confirm"|t }}{% endif %}</button>
                <button type="button" class="btn btn-default cancel-button" data-dismiss="modal">{{ "Cancel"|t }}</button>
            </div>
        </div>
    </div>
</div>