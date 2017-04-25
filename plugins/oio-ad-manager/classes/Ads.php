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


    public function getAdsByType($adType){
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

    public function logImpression($pid, $referer){
        $this->_insertImpression($pid, $referer);
    }

    public function logClick($pid, $referer){
        $time_now = time();
        
        $client_ip = $this->_getClientIPAsLong();

        if(!$this->_hasAlreadyClicked($pid, $client_ip, $time_now)){
            $this->_insertClick($pid, $client_ip, $referer, $time_now);
        }
    }

    private function _getClientIPAsLong(){
        return ip2long($_SERVER['REMOTE_ADDR']);
    }

    private function _getClientAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    }

    private function _insertImpression($pid, $referer){
        // IOI stores multiple pids separated by |'s
        //  -- A single pid is stored as 0|pid
        $pid_sep = "0|" . $pid;

        $time = time();
        $date = date('Y-m-d', $time);
        $client_ip = $this->_getClientIPAsLong();
        $agent = $this->_getClientAgent();

        $columns = array('pids','time','date','ip','agent','referer');
        $values = array($pid_sep, $time, $date, $client_ip, $agent, $referer);
        $insert = $this->db->insert($columns)
                           ->into($this->settings['db']['prefix'] . self::OIO_TABLE_TRACKER_VISITS)
                           ->values($values);

        return is_int($insert->execute(false));
    }

    private function _insertClick($pid, $client_ip, $referer, $time){
        $date = date('Y-m-d', $time);
        $agent = $this->_getClientAgent();

        $columns = array('pid','time','date','ip','agent','referer');
        $values = array($pid, $time, $date, $client_ip, $agent, $referer);
        $insert = $this->db->insert($columns)
                           ->into($this->settings['db']['prefix'] . self::OIO_TABLE_TRACKER_CLICKS)
                           ->values($values);

        return is_int($insert->execute(false));
    }

    private function _hasAlreadyClicked($pid, $client_ip, $time){
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