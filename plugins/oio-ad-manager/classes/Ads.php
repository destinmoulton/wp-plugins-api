<?php
/**
 * Methods to interface with the OIO Ad Manager tables.$_COOKIE
 *
 * @author Destin Moulton
 */

class Ads {
    const OIO_TABLE_PURCHASES = "oiopub_purchases";
    const OIO_TABLE_TRACKER_CLICKS = "oiopub_tracker_clicks";
    const OIO_TABLE_TRACKER_VISITS = "oiopub_tracker_visits";

    function __construct($db, $logger, $settings){
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = $settings;
    }

    function getAdsByType($adType){
        $types = array( 'link' => 2, 'inline' => 3, 'banner' => 5 );

        $select = $this->db->select()
                           ->from($this->settings['db']['prefix'] . self::OIO_TABLE_PURCHASES)
                           ->where("item_channel", "=", $types[$adType])
                           ->where("item_status", "=", 1)
                           ->where("payment_status", "=", 1)
                           ->where("payment_time", "<", time())
                           ->orderBy("payment_time", "DESC");

        $stmt = $select->execute();
        return $stmt->fetchAll();
    }

    function checkIfClicked($purchase_id, $ip){
        
    }
}