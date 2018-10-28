<?php

class nc_netshop_cart_notifications {

    protected $messages = array();

    /**
     * @param string $message
     * @param nc_netshop_item $item
     * @param int|float $requested_qty
     */
    public function add($message, nc_netshop_item $item, $requested_qty) {
        $this->messages[] = array(
            'message' => $message,
            'item' => $item,
            'requested_qty' => $requested_qty
        );
    }

    /**
     * @return array
     */
    public function get_all() {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function output() {
        if (!$this->messages) { return ""; }
        $result = "<div class='tpl-block-message tpl-block-netshop-cart-message-list tpl-status-error tpl-state-error'>";
        foreach ($this->messages as $message) {
            $result .= "<div class='tpl-block-netshop-cart-message'>$message[message]</div>";
        }
        $result .= "</div>";
        return $result;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->output();
    }

}