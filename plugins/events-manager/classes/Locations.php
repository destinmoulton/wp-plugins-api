<?php

class Locations {
    function __construct($db, $logger, $settings){
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = $settings;
        
        $this->locations = [];
    }

    function getCacheLocations(){
        $locations = $this->db->ltdbsem_locations()
                              ->order("location_id ASC");

        $rows = [];
        foreach ($locations as $loc){
            $rows[$loc['location_id']] = $loc;
        }
        $this->locations = $rows;
    }

    function getLocation($location_id){
        if(empty($this->locations)){
            // Cache the locations
            $this->getCacheLocations();
        }

        return (isset($this->locations[$location_id]))? $this->locations[$location_id]: "";
    }
}