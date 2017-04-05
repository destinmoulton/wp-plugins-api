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

            // Add the location data to the array
            $evData['location'] = $locations->getLocation($event['location_id']);

            // Add the attached image to the array
            $evData['image_url'] = $images->getImageForEvent($event['post_id']);
            
            $evData['post_content'] = Html2Text::convert($event['post_content']);
            
            $rows[] = $evData;
        }
        return $rows;
    }

    function _cleanString($str){
        $new = strip_tags($str);
        $new = str_replace("\xC2\xA0", ' ', html_entity_decode($new));
        $new = str_replace(array("\r", "\n", "\\r", "\\n"), ' ', $new);
        // Replace multiple spaces
        $new = preg_replace('/\s+/S', " ", $new);

        //$new = preg_replace('/ +/', ' ', $new);
        return trim($new);
    }
}