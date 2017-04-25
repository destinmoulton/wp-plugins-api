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
    const DUPLICATE_CLICK_TIME_LIMIT = 1800;

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

    function hasAlreadyClicked($pid, $client_ip){
        $date_now = date('Y-m-d', $time_now);
        $time_now = time();
        $allowed_time_difference = $time_now - self::DUPLICATE_CLICK_TIME_LIMIT;

        $select = $this->db->select()
                           ->from($this->settings['db']['prefix'] . self::OIO_TABLE_TRACKER_CLICKS)
                           ->where("pid", "=", $pid)
                           ->where("ip", "=", $client_ip)
                           ->where("time", ">", $allowed_time_difference);

        $stmt = $select->execute();
        $click = $stmt->fetch();

        return isset($click['pid']);
    }
}