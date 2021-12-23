<?php

namespace Mahesi\FetchSpotifyPodcasts;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mahesi\FetchSpotifyPodcasts\Skeleton\SkeletonClass
 */
class FetchSpotifyPodcastsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fetch-spotify-podcasts';
    }
}
