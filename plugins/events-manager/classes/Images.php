<?php

class Images {
    const META_TABLE = "postmeta";
    const POSTS_TABLE = "posts";

    function __construct($db, $logger, $settings){
        $this->db = $db;
        $this->logger = $logger;
        $this->settings = $settings;
    }

    function getImageForEvent($post_id){
        // Get the thumbnail post_id from postmeta
        $select = $this->db->select()
                         ->from($this->settings['db']['prefix'] . self::META_TABLE)
                         ->where("post_id", "=", $post_id)
                         ->where("meta_key", "=", "_thumbnail_id");

        $stmt = $select->execute();
        $image = $stmt->fetch();
        if(!isset($image['meta_value'])){
            return "";
        }

        // Get the attachment info from the postmeta table
        $select = $this->db->select()
                           ->from($this->settings['db']['prefix'] . self::META_TABLE)
                           ->where("meta_key", "=", "_wp_attachment_metadata")
                           ->where("post_id", "=", $image['meta_value']);
        $stmt = $select->execute();
        $meta = $stmt->fetch();

        if(!isset($meta['meta_value'])){
            return "";
        }

        return unserialize($meta['meta_value']);
    }
}