<?php

class Images {
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;
    }

    function getImageForEvent($post_id){
        //ltdbspostmeta _thumbnail_id
        $meta = $this->db->ltdbspostmeta()
                         ->where("post_id", $post_id)
                         ->where("meta_key", "_thumbnail_id")
                         ->limit(1);
        if(!isset($meta[0])){
            return "";
        }

        $post = $this->db->ltdbsposts()
                              ->where("ID", $meta[0]['meta_value'])
                              ->limit(1);

        return (isset($post[0]))? $post[0]['guid'] : "";
    }
}