<?php

namespace dealersleague\marine\Http;

use dealersleague\marine\ErrorHandler;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Client wrapper around Guzzle.
 *
 * @package dealersleague\marine\Http
 */
class Client extends GuzzleClient {
	public $retryLimit = 10;
	public $retryWaitSec = 10;

	/**
	 * @param string $method
	 * @param null $uri
	 * @param array $options
	 * @param bool $asJson
	 * @param bool $wantsGetContents
	 *
	 * @return mixed|\Psr\Http\Message\ResponseInterface|\Psr\Http\Message\StreamInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \dealersleague\marine\Exceptions\DealersLeagueException
	 */
	public function request( $method, $uri = null, array $options = [], $asJson = true, $wantsGetContents = true ) {
		$response = parent::request( $method, $uri, $options );

		// Support for 503 "too busy errors". Retry multiple times before failure
		$retries = 0;
		$wait    = $this->retryWaitSec;
		while ( $response->getStatusCode() === 503 and $this->retryLimit > $retries ) {
			$retries ++;
			sleep( $wait );
			$response = parent::request( $method, $uri, $options );
			// Wait 20% longer if it fails again
			$wait *= 1.2;
		}
		if ( $response->getStatusCode() !== 200 ) {
			ErrorHandler::handleErrorResponse( $response );
		}

		if ( $asJson ) {
			return json_decode( $response->getBody(), true );
		}

		if ( ! $wantsGetContents ) {
			return $response->getBody();
		}

		return $response->getBody();
	}
}
