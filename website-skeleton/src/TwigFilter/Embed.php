<?php

namespace App\TwigFilter;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Embed extends AbstractExtension{

    
    public function getFilters()
    {
        return [
            'my_youtube_embed_url' => new TwigFilter('my_youtube_embed_url', [$this, 'myYoutubeEmbedUrl'],['is_safe'=>'html'])
        ];
    }

    /**
     * Compose a youtube embed url, specify options such as autoplay
     * @param $youtubeId
     * @param array $options
     * @return string
     */
    public function myYoutubeEmbedUrl($youtubeId, $options = [])
    {
        // see: https://developers.google.com/youtube/player_parameters
        static $defaults = [
            'rel' => false,                 // show related from all channels or from own channel
            'autoplay' => false,            // set to true or false. note that for autoplaying, mute needs to be set to true
            'mute' => false,                // set to true or false.
            'enablejsapi' => false,         // set to true or false.
        ];
        $options = array_merge($defaults, $options);
        $params = [
            'rel' => (int)$options['rel'] ? '1' : '0',
            'autoplay' => (int)$options['autoplay'] ? '1' : '0',
            'mute' => (int)$options['mute'] ? '1' : '0',
            'enablejsapi' => (int)$options['enablejsapi'] ? '1' : '0',
        ];
        $parts = [];
        foreach ($params as $k => $v) {
            $parts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        return 'https://www.youtube.com/embed/' . rawurlencode($youtubeId) . '?' . implode('&', $parts);
    }
}

?>