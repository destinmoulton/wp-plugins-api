<?php


class Events{
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;
    }

    function getDateRange($start_date, $end_date){
        $this->logger->addInfo($end_date);
        $events = $this->db->ltdbsem_events()
                            ->where("event_start_date >= ? AND event_end_date <= ?", $start_date, $end_date)
                            ->order("event_id DESC")
                            ->limit(30);

        $this->logger->addInfo((string) $events);
        $rows = [];
        foreach ($events as $event){
            $rows[] = $event;
        }
        return $rows;
    }
}