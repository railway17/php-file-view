<?php
    $ROOT_PATH = '/file';
    $requestURL = strtok($_SERVER["REQUEST_URI"], '?');
    
    require_once('header.php');
    if($requestURL == $ROOT_PATH.'/documents') {
        require_once('Contents/documents.php');
    } else if($requestURL == $ROOT_PATH.'/folders') {
        require_once('Contents/folders.php');
    }
    require_once('footer.php');
?>