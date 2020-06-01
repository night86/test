<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\MapSignaLedgerTariff;
use Signa\Models\CodeLedger;
use Signa\Models\CodeTariff;

class MapController extends InitController
{
    public function indexAction(){

        if($this->request->isPost()){

            $post = $this->request->getPost();
            $status = false;

            foreach($post as $key => $tarrif_id){

                $ledger_id = (int)str_replace('ledger-', '', $key);
                $map = MapSignaLedgerTariff::findFirst('ledger_id = '.$ledger_id);

                // If map exist and value = 0 then remove map
                if($map && $tarrif_id == 0){

                    $map->delete();
                    $status = true;
                }

                // If map doesn't exist then create new map object
                if($map == false){
                    $map = new MapSignaLedgerTariff();
                }

                // If tariff code is selected then assigna values to new map object or update old object
                if($tarrif_id > 0 && $map->getTariffId() !== $tarrif_id){

                    $map->setLedgerId($ledger_id);
                    $map->setTariffId($tarrif_id);
                    $map->save();
                    $status = true;
                }
            }
            return json_encode(array('status' => $status));
        }

        $this->assets->collection('footer')
            ->addJs("js/app/mapSigna.js");

        $maps = MapSignaLedgerTariff::find();
        $mapsArr = array();

        foreach ($maps as $map){

            $mapsArr[$map->getLedgerId()] = (int)$map->getTariffId();
        }
        $this->view->maps = $mapsArr;
        $this->view->ledgers = CodeLedger::find('organisation_id ='.$this->currentUser->getOrganisationId());
        $this->view->tariffs = CodeTariff::find('organisation_id ='.$this->currentUser->getOrganisationId());
    }
}
