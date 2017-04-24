<?php
/**
 * Methods to interface with the OIO Ad Manager tables.$_COOKIE
 *
 * @author Destin Moulton
 */

class Ads {
    const OIO_TABLE_PURCHASES = "ltdbsoiopub_purchases";

    function __construct($db, $logger, $settings){
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = $settings;
    }

    function getAdsByType($adType){
        //The types of ads
        $types = array( 'link' => 2, 'inline' => 3, 'banner' => 5 );


        $sql = "SELECT * FROM " . self::OIO_TABLE_PURCHASES . " WHERE item_channel=" . $types[$type] . " AND item_status=1 AND payment_status=1" . ($zones ? " AND item_type IN(" . implode(",", $zones) . ")" : "") . " AND (payment_time=0 OR payment_time < " . time() . ")" . $sql_extra . " ORDER BY payment_time DESC";
    }
}