<?php

namespace Mahesi\FetchSpotifyPodcasts;

use Spotify;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Throwable;

class FetchSpotifyPodcasts {

	private mixed $httpHeader;
	private mixed $apiUrl;
	private mixed $market;
	private mixed $limit;

	public function __construct() {
		$this->httpHeader = config( 'fetch-spotify-podcasts.http-header' );
		$this->apiUrl     = config( 'fetch-spotify-podcasts.api-url' );
		$this->market     = config( 'fetch-spotify-podcasts.spotify-market' );
		$this->limit      = config( 'fetch-spotify-podcasts.response-limit' );
	}

	public function fetchEpisodes( $spotifyId = false ) {
		if ( ! $spotifyId ) {
			return [];
		}

		$offset     = 0;
		$limit      = $this->limit;
		$pagination = true;
		$episodes   = [];

		while ( $pagination ) {
			$response = Spotify::showEpisodes( $spotifyId )->market( $this->market )->offset( $offset )->limit( $limit )->get();
			foreach ( $response['items'] as $episode ) {
				$episodes[] = $episode;
			}
			$pagination = ! ( $response['next'] === null );
			$offset     = $response['next'] !== null ? $offset + $limit : $offset;
		}

		return $episodes;
	}

	/**
	 * @throws \Aerni\Spotify\Exceptions\SpotifyApiException
	 * @throws \Aerni\Spotify\Exceptions\ValidatorException
	 */
	public function fetchPodcasts(): array {
		$urlContent = Http::withHeaders( $this->httpHeader )->get( $this->apiUrl );

		$dom = new DOMDocument();
		@$dom->loadHTML( $urlContent );

		$xpath = new DOMXPath( $dom );
		$hrefs = $xpath->evaluate( "/html/body//a" );

		$categoryLinks = [];

		// Collect all category links
		for ( $i = 0; $i < $hrefs->length; $i ++ ) {
			$href = $hrefs->item( $i );
			$url  = $href->getAttribute( 'href' );
			$url  = filter_var( $url, FILTER_SANITIZE_URL );
			if ( ( ! filter_var( $url, FILTER_VALIDATE_URL ) === false ) && preg_match( config( 'fetch-spotify-podcasts.regex-chartable-categories' ), $url ) ) {
				$categoryLinks[] = $url;
			}
		}

		$podcasts = [];
		foreach ( $categoryLinks as $key => $categoryURL ) {
			$urlContent = Http::withHeaders( $this->httpHeader )->get( $categoryURL );

			$dom = new DOMDocument();
			@$dom->loadHTML( $urlContent );

			$xpath = new DOMXPath( $dom );
			$hrefs = $xpath->evaluate( "/html/body//a" );

			for ( $i = 0; $i < $hrefs->length; $i ++ ) {
				$href = $hrefs->item( $i );
				$url  = $href->getAttribute( 'href' );
				$url  = filter_var( $url, FILTER_SANITIZE_URL );
				if (
					$href->nodeValue !== '' &&
					( ! filter_var( $url, FILTER_VALIDATE_URL ) === false ) && preg_match( config( 'fetch-spotify-podcasts.regex-chartable-podcasts' ), $url )
				) {
					foreach ( $this->getSpotifyPodcastsByName( $href->nodeValue ) as $spotifyPodcast ) {
						$podcasts[] = $spotifyPodcast;
					}
				}
			}
		}

		return $podcasts;
	}

	public function fetchPodcast( $spotifyId = false ): array {
		if ( ! $spotifyId ) {
			return [ 'error' => 'id not given' ];
		}
		try {
			return Spotify::show( $spotifyId )
			              ->market( $this->market )
			              ->get();
		} catch ( Throwable $e ) {
			return [ 'error' => $e->getMessage() ];
		}
	}

	/**
	 * @throws \Aerni\Spotify\Exceptions\ValidatorException
	 * @throws \Aerni\Spotify\Exceptions\SpotifyApiException
	 */
	private function getSpotifyPodcastsByName( $podcastName = false ): bool|array {
		if ( ! $podcastName || empty( $podcastName ) ) {
			return [];
		}

		$shows    = Spotify::searchShows( $podcastName )
		                   ->market( $this->market )
		                   ->limit( $this->limit )
		                   ->offset( 0 )
		                   ->includeExternal( 'audio' )
		                   ->get();
		$podcasts = [];

		foreach ( $shows['shows']['items'] as $show ) {
			if ( in_array( 'de', $show['languages'], true ) ) {
				$podcasts[] = [
					'id'          => $show['id'],
					'name'        => $show['name'],
					'description' => $show['description'],
					'publisher'   => $show['publisher'],
					'image'       => $show['images'][0]['url'],
					'lang'        => $show['languages'][0],
					'episodes'    => $show['total_episodes'],
					'url'         => $show['external_urls']['spotify']
				];
			}
		}

		return $podcasts;
	}
}
