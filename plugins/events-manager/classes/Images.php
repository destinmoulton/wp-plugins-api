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

        $unserialized = unserialize($meta['meta_value']);

        if(!isset($unserialized['file'])){
            return "";
        }

        return $unserialized['file'];
    }

    function getImageSize($imageURL){
        if(!$imageURL){
            return array('image_width'=>0, 'image_height'=>0);
        }
        // Replace the site URL with the absolute path
        $image_path = str_replace($this->settings['wp']['url'], $this->settings['wp']['absolute_path'], $imageURL);
        
        if(!file_exists($image_path)){
            return array('image_width'=>0, 'image_height'=>0);
        }

        $size = getimagesize($image_path);
        return array('image_width'=>$size[0], 'image_height'=>$size[1]);
    }
}