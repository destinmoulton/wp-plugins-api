<?php

require('Locations.php');
require('Images.php');

class Events{
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;

    }

    function getForDate($date){
        $locations = new Locations($this->db, $this->logger);
        $images = new Images($this->db, $this->logger);

        $events = $this->db->ltdbsem_events()
                            ->where("event_start_date = ?", $date)
                            ->order("event_start_date ASC, event_start_time ASC")
                            ->limit(30);

        $rows = [];
        foreach ($events as $event){
            $evData = $event;

            // Add the location data to the array
            $evData['location'] = $locations->getLocation($event['location_id']);

            // Add the attached image to the array
            $evData['image_url'] = $images->getImageForEvent($event['post_id']);
            $rows[] = $evData;
        }
        return $rows;
    }
}