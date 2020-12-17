<?php

/**
 * Check if the raw post data is a valid json (in respect of the the given fields).
 *
 * If at least one of the given fields is not inside the post data, the validate returns null.
 * @param string $raw_post
 * @param string ...$fields
 * @return array|null returns an associative array obtained from the raw post string, or null if the json is not valid.
 */
function validatePost($raw_post, ...$fields){
    $post = json_decode($raw_post, true);
    if ($post === null)
        return null;
    
    foreach($fields as $field)
        if (!array_key_exists($field, $post))
            return null;
            
    return $post;
}
?>