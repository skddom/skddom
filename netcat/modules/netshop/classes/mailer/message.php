<?php

class nc_netshop_mailer_message {

    protected $subject;
    protected $body;

    /**
     *
     */
    public function __construct($subject, $body) {
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     *
     */
    public function get_subject() {
        return $this->subject;
    }

    /**
     *
     */
    public function get_body() {
        return $this->body;
    }

}