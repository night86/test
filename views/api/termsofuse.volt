{% extends "layouts/main.volt" %}
{% block title %} {{ "Terms of use"|t }} {% endblock %}
{% block content %}

    <h3>Privacy Policy/ Gebruikersvoorwaarden</h3><br />

    <form action="/api/termsofuse" method="post">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <p>Privacy policy voor het gebruikersplatform Signadens</p>
            <h3>1) Waarborgen Privacy</h3>

            <p>Het waarborgen van de privacy van gebruikers van   is een belangrijke taak voor ons. <br />
                Daarom beschrijven we in onze privacy policy welke informatie we verzamelen en hoe we deze <br />
                informatie gebruiken. Het Signadens platform biedt gebruikers de mogelijkheid om opdracht (patiënt) <br />
                informatie uit te wisselen op een manier waarbij de privacy van gebruikers en patiënten gewaarborgd <br />
                is. De gebruikers van het Signadens platform zijn zelfstandige bedrijven die actief zijn in de <br />mondzorgverlening.</p>
            <p>Het Signadens platform is gebouwd conform de laatste technieken en voldoet aan de eisen om <br />
                privacygevoelige informatie op een correcte manier op te kunnen slaan en beschikbaar te stellen voor <br />
                opdrachtgevers en een verwerkende partij. </p>
            <p>Op basis van de AVG-wetgeving is het noodzakelijk dat er tussen opdrachtgever en verwerker een <br />
                zogeheten verwerkersovereenkomst opgesteld is. Door hieronder een vinkje te zetten, bevestigt u dat <br />
                u daarvan op de hoogte bent en dat u samen met uw verwerkende partij zal zorgen voor een <br />
                ondertekende verwerkersovereenkomst. Een concept verwerkersovereenkomst is door uw <br />
                verwerkende partij reeds ondertekend en staat voor u klaar ter ondertekening zodra u voor de eerste <br />
                keer inlogt in het platform. Hierin is het gebruik van het Signadens platform als zijnde sub-verwerker <br />opgenomen.</p>
            <p>Alle gebruikers van het Signadens platform bevestigen dat zij op de hoogte zijn van het feit dat zij <br />
                omgaan met veelal privacygevoelige informatie en dat zij dienovereenkomstig dienen te handelen. <br />
                Hoe partijen naast het gebruik van het Signadens platform de informatie intern behandelen blijft de <br />
                verantwoordelijkheid van de gebruikers. Wij adviseren u waar mogelijk zo min mogelijk <br />
                privacygevoelige informatie uit te printen en waar mogelijk alleen die informatie aan te leveren die voor <br />
                verwerkers <b><u>strikt noodzakelijk</u></b> is om de opdracht uit te kunnen voeren.</p>
            <h3>2) Toestemming</h3>
            <p>Door de informatie en de diensten op www.  te gebruiken, gaat u akkoord met onze <br />
                privacy policy en de voorwaarden die wij hierin hebben opgenomen.</p>
            <h3>3) Vragen</h3>
            <p>Wanneer u meer informatie wilt ontvangen, of vragen hebt over de privacy policy van het gebruikersplatform Signadens, <br />kunt u ons benaderen via e-mail. Ons e-mailadres is <a href="mailto:info@ ">info@ </a>.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <input id="agree1" type="checkbox" name="agree1" />
                <label for="agree1">Ik bevestig dat wij een zogeheten verwerkersovereenkomst zullen ondertekenen t.b.v. de bescherming van privacygevoelige informatie.</label>
            </div>
        </div>
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <input id="agree2" type="checkbox" name="agree2" />
                <label for="agree2">Ik bevestig dat wij akkoord gaan met de algemene voorwaarden en condities zoals hierboven omschreven.</label>
            </div>
        </div>
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <input type="hidden" name="status" value="{{ _GET['status'] }}" />
                <input type="hidden" name="email" value="{{ _GET['email'] }}" />
                <input type="hidden" name="name" value="{{ _GET['name'] }}" />
                <input type="hidden" name="lab" value="{{ _GET['lab'] }}" />
                <input type="hidden" name="den" value="{{ _GET['den'] }}" />
                <input id="confirm_terms" type="submit" name="accept_terms" class="btn btn-primary" value="{{ "Inloggen"|t }}" disabled="disabled" />
            </div>
        </div>
    </div>
    </form>
{% endblock %}

{% block scripts %}
    {{ super() }}

    <script>
        $(function(){

            $("#agree1, #agree2").click(function () {
                if ($("#agree1").is(':checked') == true && $("#agree2").is(':checked') == true) {
                    $("#confirm_terms").removeAttr("disabled");
                } else {
                    $("#confirm_terms").attr("disabled", "disabled");
                }
            });
        });
    </script>
{% endblock %}