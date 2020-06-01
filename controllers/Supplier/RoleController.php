<?php

namespace Signa\Controllers\Supplier;

use Signa\Models\OrganisationTypes;
use Signa\Models\RoleTemplates;
use Signa\Models\Roles;

class RoleController extends \Signa\Controllers\Signadens\RoleController
{
    public function indexAction(){

        $this->view->roles = RoleTemplates::find('deleted = 0 AND organisation_type_id = '.$this->currentUser->Organisation->getOrganisationTypeId());
        $this->view->active = array($this->t->make('No'),$this->t->make('Yes'));
    }

    public function addAction(){

        parent::addAction();

        $this->view->currentOrganisationType = $this->currentUser->Organisation->getOrganisationTypeId();
    }

    public function editAction($id)
    {
        parent::editAction($id);

        $this->view->currentOrganisationType = $this->currentUser->Organisation->getOrganisationTypeId();
    }
}
