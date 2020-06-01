<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 19.07.2016
 * Time: 09:33
 */

namespace Signa\Controllers\Signadens;


abstract class CrudController extends InitController
{
    abstract public function IndexAction();
    abstract public function AddAction();
    abstract public function EditAction($id);
    abstract public function DeleteAction($id);
}