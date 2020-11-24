<?php

namespace dealersleague\marine;

use dealersleague\marine\Exceptions\NotFoundException;
use dealersleague\marine\Exceptions\ValidationException;
use dealersleague\marine\Http\Client as HttpClient;

class Client {
	protected $userEmail;
	protected $apiKey;

	protected $authToken;
	protected $apiUrl = 'https://api.dlcrm.local/v1/';

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
			$this->client = new HttpClient( [ 'exceptions' => false ] );
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

	public function getSettings() {

		$uri      = '/settings/get';
		$response = $this->request( 'GET', $uri );
	}

	/**
	 * Get a listings page based on the given search options.
	 *
	 * @param int $currentPage
	 * @param array $searchOptions
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getListingsPage( $currentPage = 0, $searchOptions = [] ) {

		$uri = '/listing/get?page=' . $currentPage;
		$options = empty( $searchOptions ) ? [] : [ 'body' => $searchOptions ];
		$response = $this->request( 'POST', $uri, $options );

	}

	/**
	 * Get a single listing with the given id
	 *
	 * @param $listingId
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getSingleListing( $listingId ) {

		$uri      = '/listing/get?id=' . $listingId;
		$response = $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all brokers
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getBrokers() {

		$uri = '/broker/get';
		$response = $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all locations
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getLocations() {

		$uri = '/location/get';
		$response = $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all categories
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getCategories() {

		$uri = 'category/get';
		$response = $this->request( 'GET', $uri );

	}

	/**
	 * Get a list with all manufacturers
	 *
	 * @throws Exceptions\DealersLeagueException
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getManufacturers() {

		$uri = '/manufacturer/get';
		$response = $this->request( 'GET', $uri );

	}


}
