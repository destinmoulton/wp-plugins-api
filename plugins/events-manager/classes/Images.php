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

        if(!isset($post['guid'])){
            return "";
        }

        return $post['guid'];
    }

    function getImageSize($imageURL){
        if(!$imageURL){
            return array('image_width'=>0, 'image_height'=>0);
        }
        // Replace the site URL with the absolute path
        $image_path = str_replace($this->settings['wp']['url'], $this->settings['wp']['absolute_path'], $imageURL);
        $size = getimagesize($image_path);
        return array('image_width'=>$size[0], 'image_height'=>$size[1]);
    }
}