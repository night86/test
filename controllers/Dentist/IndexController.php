<?php

namespace Signa\Controllers\Dentist;

use Signa\Helpers\User;
use Signa\Models\DentistOrderRecipeDelivery;
use Signa\Models\LabDentists;
use Signa\Models\Organisations;
use Signa\Helpers\Translations as Trans;
use Signa\Models\OrganisationTypes;
use Signa\Models\Users;

class IndexController extends InitController
{
    const LAB_ORGANISATION_ID = 4;

    public function indexAction(){

        $labDentist = LabDentists::find('dentist_id = '.$this->currentUser->getId());

        if (count($labDentist) > 0) {
            $logosArray = [];

            foreach($labDentist as $ld){

                if($ld->Dentist){
                    $logosArray[] = $ld->Dentist->getLogo();
                }
            }
            $this->view->labLogo = $logosArray;
        }

        // View vars and assets
        $this->view->disableSubnav = false;
        $this->assets->collection('footer')
            ->addJs("bower_components/moment/moment.js")
            ->addJs("bower_components/fullcalendar/dist/fullcalendar.js")
            ->addJs("bower_components/fullcalendar/dist/locale/nl.js")
            ->addJs("js/app/dentist.js");
    }

    public function startAction(){

        // View vars and assets
        $this->view->startPage = OrganisationTypes::findFirst("slug = 'dentist'")->getStartPage();
    }

    public function ajaxcalendarAction(){

        $users = [];
        $findOrgUsers = Users::find('organisation_id = '.$this->currentUser->getOrganisationId().' AND active = 1');

        foreach($findOrgUsers as $u){

            $users[] = $u->getId();
        }

        $allUsers = implode(",", $users);

        $recipeDeliveries = DentistOrderRecipeDelivery::find("created_by IN (".$allUsers.") AND delivery_date >= '".$this->request->get('start')."' AND delivery_date <= '".$this->request->get('end')."' ORDER BY delivery_date ASC");
        $resultsArr = [];
        $i = 0;

        foreach ($recipeDeliveries as $rd){

            if($rd->getDeliveryDate() != NULL && in_array($rd->DentistOrderRecipe->DentistOrder->getStatus(), [2,3,4,5])){

                $description = '<p>'.Trans::make("Patient name").': '.$rd->DentistOrderRecipe->DentistOrder->DentistOrderData->getPatientInitials().' '.$rd->DentistOrderRecipe->DentistOrder->DentistOrderData->getPatientInsertion().' '.$rd->DentistOrderRecipe->DentistOrder->DentistOrderData->getPatientLastname().'</p>';
                $description .= '<p>'.Trans::make("Recipe name").': '.$rd->DentistOrderRecipe->Recipes->getName().' / '.$rd->DentistOrderRecipe->Recipes->getRecipeNumber().'</p>';

                if($rd->DentistOrderRecipe->DentistOrder->DentistUser){
                    $description .= '<p>'.Trans::make("Dentist name").': '.$rd->DentistOrderRecipe->DentistOrder->DentistUser->getFirstname().' '.$rd->DentistOrderRecipe->DentistOrder->DentistUser->getLastname().'</p>';
                }
                else {
                    $description .= '<p>'.Trans::make("Dentist name").': </p>';
                }

                if(in_array($rd->DentistOrderRecipe->DentistOrder->getStatus(), [2,3])){
                    
                    $description .= '<p>'.Trans::make("Order code").': <a id="order_'.$rd->DentistOrderRecipe->DentistOrder->getCode().'" href="/dentist/order/edit/'.$rd->DentistOrderRecipe->DentistOrder->getCode().'">'.$rd->DentistOrderRecipe->DentistOrder->getCode().'</a></p>';
                }
                else {
                    $description .= '<p>'.Trans::make("Order code").': <a id="order_'.$rd->DentistOrderRecipe->DentistOrder->getCode().'" href="/dentist/order/view/'.$rd->DentistOrderRecipe->DentistOrder->getCode().'">'.$rd->DentistOrderRecipe->DentistOrder->getCode().'</a></p>';
                }

                if($rd->getPartOfDay() != NULL){
                    $description .= '<p>'.Trans::make("Part of day").': '.$rd->getPartOfDay().'</p>';
                }
                $description .= '<p style="display: inline-block;">'.Trans::make("Name of phase").': '.$rd->getDeliveryText().'</p>';
                $description .= '<p style="display: inline-block;"><img src="/uploads/images/organisation/'.$rd->DentistOrderRecipe->Recipes->Lab->getLogo().'" style="width: 40%; float: right;" /></p><br />';

                $resultsArr[$i]['description'] = $description;
                $resultsArr[$i]['start'] = $rd->getDeliveryDate();
                $resultsArr[$i]['type'] = $rd->DentistOrderRecipe->DentistOrder->getStatus();
                $i++;
            }
        }
        return json_encode($resultsArr);
    }
}
