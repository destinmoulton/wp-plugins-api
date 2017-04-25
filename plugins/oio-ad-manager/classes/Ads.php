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

    function _getClientIPAsLong(){
        return ip2long($_SERVER['REMOTE_ADDR']);
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

    function logImpression($pid, $referer){

    }

    function logClick($pid, $referer){
        $time_now = time();
        
        $client_ip = $this->_getClientIPAsLong();

        if(!$this->hasAlreadyClicked($pid, $client_ip, $time_now)){
            $this->insertClick($pid, $client_ip, $referer, $time_now);
        }
    }

    function insertClick($pid, $client_ip, $referer, $time){
        $date = date('Y-m-d', $time);
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $columns = array('pid','time','date','ip','agent','referer');
        $values = array($pid, $time, $date, $client_ip, $agent, $referer);
        $insert = $this->db->insert($columns)
                           ->into($this->settings['db']['prefix'] . self::OIO_TABLE_TRACKER_CLICKS)
                           ->values($values);
        return is_int($insert->execute(false));
    }

    function hasAlreadyClicked($pid, $client_ip, $time){
        $allowed_time_difference = $time - self::DUPLICATE_CLICK_TIME_LIMIT;

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