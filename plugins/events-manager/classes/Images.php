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

        // Get the post with the thumbnail info
        $select = $this->db->select()
                           ->from($this->settings['db']['prefix'] . self::POSTS_TABLE)
                           ->where("ID", "=", $image['meta_value']);
        $stmt = $select->execute();
        $post = $stmt->fetch();

        return (isset($post['guid']))? $post['guid'] : "";
    }

    function _getImageSize($post){

    }
}