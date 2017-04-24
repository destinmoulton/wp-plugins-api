<?php
/**
 * Methods to interface with the OIO Ad Manager tables.$_COOKIE
 *
 * @author Destin Moulton
 */

class Ads {
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;
    }

    function getAdsByType($adType){
        //The types of ads
        $types = array( 'link' => 2, 'inline' => 3, 'banner' => 5 );

        $sql = "SELECT * FROM " . $this->settings->dbtable_purchases . " WHERE item_channel=" . $types[$type] . " AND item_status=1 AND payment_status=1" . ($zones ? " AND item_type IN(" . implode(",", $zones) . ")" : "") . " AND (payment_time=0 OR payment_time < " . time() . ")" . $sql_extra . " ORDER BY payment_time DESC";
    }
}