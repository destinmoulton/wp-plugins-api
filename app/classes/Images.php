<?php

class Images {
    function __construct($db, $logger){
        $this->db = $db;
        $this->logger = $logger;
    }

    function getImageForEvent($post_id){
        $attachment = $this->db->ltdbsposts()
                              ->where("post_parent", $post_id)
                              ->where("post_status = 'inherit'")
                              ->where("post_type = 'attachment'")
                              ->limit(1);

        return ($attachment)? $attachment->guid : "";
    }
}