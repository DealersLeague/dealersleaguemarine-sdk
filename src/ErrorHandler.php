<?php

namespace dealersleague\marine;

use dealersleague\marine\Exceptions\DealersLeagueException;
use dealersleague\marine\Exceptions\BadJsonException;
use dealersleague\marine\Exceptions\BadValueException;
use dealersleague\marine\Exceptions\BucketAlreadyExistsException;
use dealersleague\marine\Exceptions\BucketNotEmptyException;
use dealersleague\marine\Exceptions\FileNotPresentException;
use dealersleague\marine\Exceptions\NotFoundException;
use GuzzleHttp\Psr7\Response;

class ErrorHandler
{
    protected static $mappings = [
        'bad_json'                       => BadJsonException::class,
        'bad_value'                      => BadValueException::class,
        'duplicate_bucket_name'          => BucketAlreadyExistsException::class,
        'not_found'                      => NotFoundException::class,
        'file_not_present'               => FileNotPresentException::class,
        'cannot_delete_non_empty_bucket' => BucketNotEmptyException::class,
    ];

	/**
	 * @param Response $response
	 *
	 * @throws DealersLeagueException
	 */
    public static function handleErrorResponse(Response $response)
    {
        $responseJson = json_decode($response->getBody(), true);

        if (isset(self::$mappings[$responseJson['code']])) {
            $exceptionClass = self::$mappings[$responseJson['code']];
        } else {
            // We don't have an exception mapped to this response error, throw generic exception
            $exceptionClass = DealersLeagueException::class;
        }

        throw new $exceptionClass(sprintf('Received error from Dealers League Marine: %s. Code: %s', $responseJson['message'], $responseJson['code']));
    }
}
