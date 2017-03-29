<?php

require('Locations.php');

class Events{
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;

    }

    function getDateRange($start_date, $end_date){
        $locations = new Locations($this->db, $this->logger);

        $events = $this->db->ltdbsem_events()
                            ->where("event_start_date >= ? AND event_end_date <= ?", $start_date, $end_date)
                            ->order("event_start_date ASC, event_start_time ASC")
                            ->limit(30);

        $rows = [];
        foreach ($events as $event){
            $evData = $event;

            // Add the location data to the array
            $evData['location'] = $locations->getLocation($event['location_id']);

            $rows[] = $evData;
        }
        return $rows;
    }
}