<?php

use Phalcon\Mvc\User\Plugin;

/**
 * String only messages as an alternative to html formatted flash messages.
 */
class Messages extends Plugin {

    public function set($message = null){
        if(empty($message)) return false;

        $this->session->set("message", $message);
    }

    public function has() {
        return $this->session->has("message");
    }

    public function output() {
        if($this->has()){
            $return = $this->session->get("message");
            $this->session->remove("message");
            return $return;
        }

        return false;
    }
}