<?php

namespace AppBundle;

/**
 * Class Helpers
 *
 * @package AppBundle
 */
class Helpers {
    /**
     * Get Youtube Id from Youtube link
     *
     * @param $link
     */
    public function getYoutubeId($link)
    {
        return preg_replace('/https?:\/\/www.youtube.com\/watch\?v=(.*)/', '$1', $link);
    }
    
}
