<?php

namespace dealersleague\marine;

use dealersleague\marine\Exceptions\NotFoundException;
use dealersleague\marine\Exceptions\ValidationException;
use dealersleague\marine\Http\Client as HttpClient;

class Client {
	protected $userEmail;
	protected $apiKey;

	protected $authToken;
	protected $apiUrl = 'https://providedurl.com/v1/';

	protected $client;

	/**
	 * Client constructor. Accepts the user email, API key and an optional array of options.
	 *
	 * @param string $userEmail
	 * @param string $apiKey
	 * @param array $options
	 *
	 * @throws \Exception
	 */
	public function __construct( string $userEmail, string $apiKey, array $options = [] ) {

		if ( empty( $userEmail ) ) {
			throw new \Exception( 'Please provide "User email"' );
		}
		$this->userEmail = $userEmail;

		if ( empty( $apiKey ) ) {
			throw new \Exception( 'Please provide "API Key"' );
		}
		$this->apiKey = $apiKey;

		if ( isset( $options[ 'client' ] ) ) {
			$this->client = $options[ 'client' ];
		} else {
			$this->client = new HttpClient( [ 'exceptions' => false, 'verify' => false] );
		}

	}

	/**
	 * @param $url
	 */
	public function setApiUrl( $url ) {
		$this->apiUrl = $url;
	}

	/**
	 * Wrapper for $this->client->request
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array $options
	 * @param bool $asJson
	 * @param bool $wantsGetContents
	 *
	 * @return mixed|string
	 * @throws \GuzzleHttp\Exception\GuzzleException|Exceptions\DealersLeagueException
	 */
	protected function request( $method = 'POST', $uri = null, array $options = [], $asJson = true, $wantsGetContents = true ) {
		$headers = [];

		if ( ! empty( $this->userEmail ) && ! empty( $this->apiKey ) ) {
			$headers[ 'Authorization' ] = 'Basic ' . base64_encode( $this->userEmail . ':' . $this->apiKey );
		}

		$options = array_replace_recursive( [ 'headers' => $headers ], $options );

		$fullUri = $uri;

		if ( substr( $uri, 0, 8 ) !== 'https://' ) {
			$fullUri = $this->apiUrl . $uri;
		}

		return $this->client->request( $method, $fullUri, $options, $asJson, $wantsGetContents );
	}

	/**
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getSettings() {

		$uri      = '/settings/get';
		return $this->request( 'GET', $uri );
	}

	/**
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getIntegrations() {

		$uri      = '/integration/get';
		return $this->request( 'GET', $uri );
	}

	/**
	 *  Get a listings page based on the given search options.
	 *
	 * @param int $currentPage
	 * @param array $searchOptions
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getListingsPage( $currentPage = 0, $searchOptions = [] ) {

		$uri = '/listing/get?page=' . $currentPage;
		$options = empty( $searchOptions ) ? [] : [ 'body' => $searchOptions ];
		return $this->request( 'POST', $uri, $options );

	}

	/**
	 *  Get a brokers page based on the given search options.
	 *
	 * @param int $currentPage
	 * @param array $searchOptions
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getBrokersPage( $currentPage = 0, $searchOptions = [] ) {

		$uri = '/broker/get?page=' . $currentPage;
		$options = empty( $searchOptions ) ? [] : [ 'body' => $searchOptions ];
		return $this->request( 'POST', $uri, $options );

	}

	/**
	 * Get a single listing with the given id
	 *
	 * @param $listingId
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getSingleListing( $listingId ) {

		$uri      = '/listing/get?id=' . $listingId;
		return $this->request( 'GET', $uri );

	}

	// /**
	//  * Get a list with all brokers
	//  *
	//  * @return mixed|string
	//  * @throws Exceptions\DealersLeagueException
	//  * @throws \GuzzleHttp\Exception\GuzzleException
	//  */
	// public function getBrokers() {

	// 	$uri = '/broker/get';
	// 	return $this->request( 'GET', $uri );

	// }

	// /**
	//  * Get a list with all locations
	//  *
	//  * @return mixed|string
	//  * @throws Exceptions\DealersLeagueException
	//  * @throws \GuzzleHttp\Exception\GuzzleException
	//  */
	// public function getLocations() {

	// 	$uri = '/location/get';
	// 	return $this->request( 'GET', $uri );

	// }

	/**
	 * Get a list with all countries
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getCountries() {

		$uri = '/country/get';
		return $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all colour tags
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getColourTags() {

		$uri = '/colour/get';
		return $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all categories
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getCategories() {

		$uri = '/category/get';
		return $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all manufacturers
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getManufacturers() {

		$uri = '/manufacturer/get';
		return $this->request( 'GET', $uri );

	}

	/**
	 *  Send listings statistics
	 *
	 * @param array $listingAnalyticList
	 *
	 * @return mixed|string
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function sendAnalytics( array $listingAnalyticList ) {

		$uri = '/listing/analytics';
		$options = empty( $listingAnalyticList ) ? [] : [ 'body' => $listingAnalyticList ];
		return $this->request( 'POST', $uri, $options );

	}


}
