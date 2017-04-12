<?php
require(__DIR__.'/../lib/html2text/Html2Text.php');

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
                            ->where("event_start_date = ? AND event_status = 1 AND recurrence = 0", $date)
                            ->order("event_start_date ASC, event_start_time ASC")
                            ->limit(30);

        $rows = [];
        foreach ($events as $event){
            $evData = $event;

            $evData['event_name'] = html_entity_decode($event['event_name']);

            $evData['event_attributes'] = unserialize($event['event_attributes']);

            // Add the location data to the array
            $evData['location'] = $locations->getLocation($event['location_id']);

            // Add the attached image to the array
            $evData['image_url'] = $images->getImageForEvent($event['post_id']);
            
            // Convert the html content into text
            //    NOTE: the second parameter needs to be true so errors in html are hidden
            $evData['post_content'] = Html2Text::convert($event['post_content'], true);
            
            $rows[] = $evData;
        }
        return $rows;
    }

    function getSingleByID($event_id){
        $locations = new Locations($this->db, $this->logger);
        $images = new Images($this->db, $this->logger);
        $event = $this->db->ltdbsem_events("event_id", $event_id)->fetch();

        $evData = $event;

        $evData['event_name'] = html_entity_decode($event['event_name']);

        $evData['event_attributes'] = unserialize($event['event_attributes']);

        // Add the location data to the array
        $evData['location'] = $locations->getLocation($event['location_id']);

        // Add the attached image to the array
        $evData['image_url'] = $images->getImageForEvent($event['post_id']);
        
        // Convert the html content into text
        //    NOTE: the second parameter needs to be true so errors in html are hidden
        $evData['post_content'] = Html2Text::convert($event['post_content'], true);
          
        return $evData;
    }
}