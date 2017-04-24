<?php

class Locations {
    const EVENT_LOCATIONS_TABLE = "em_locations";

    function __construct($db, $logger, $settings){
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = $settings;

        $this->locations = [];
    }

    function getCacheLocations(){
        $locations = $this->db->select()
                              ->from($this->settings['db']['prefix'] . self::EVENT_LOCATIONS_TABLE)
                              ->orderBy("location_id", "ASC");

        $stmt = $select->execute();
        $locations = $stmt->fetchAll();

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