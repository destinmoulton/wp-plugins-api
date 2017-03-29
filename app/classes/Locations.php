<?php

class Locations {
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;

        $this->locations = [];
    }

    function getCacheLocations(){
        $locations = $this->db->ltdbsem_locations()
                              ->order("location_id ASC");

        $rows = [];
        foreach ($locations as $loc){
            $rows[] = $loc;
        }
        $this->locations = $rows;
    }
}