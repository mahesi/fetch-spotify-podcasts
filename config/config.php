<?php

return [
	// Chartable URL for fetching categories
	'api-url'                    => 'https://chartable.com/charts/spotify/de',

	// Header for fetching with HTTP
	'http-header'                => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0',

	// URL selector for chartable category links
	'regex-chartable-categories' => '/\/charts\/spotify\/germany-[-a-zA-Z]+$/',

	// URL selector for chartable podcast links
	'regex-chartable-podcasts'   => '/\/podcasts\/[-a-zA-Z]+$/',

	// Preferred Market
	'spotify-market' => 'DE',

	// Preferred response limit (max. 50)
	'response-limit' => 50
];