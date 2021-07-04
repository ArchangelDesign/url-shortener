<?php

namespace App\Exceptions;

/**
 * Thrown when URL is longer than ShortenerService::MAX_URL_LENGTH
 *
 * Class UrlIsTooLong
 * @package App\Exceptions
 */
class UrlIsTooLong extends \Exception
{

}
