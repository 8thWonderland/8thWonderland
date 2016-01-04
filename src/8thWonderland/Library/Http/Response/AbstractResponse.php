<?php

namespace Wonderland\Library\Http\Response;

abstract class AbstractResponse
{
    /** @var array **/
    protected $primitiveHeaders = [];
    /** @var array **/
    protected $userHeaders = [];
    /** @var int **/
    protected $status;
    /** @var mixed **/
    protected $data;
    /** @var array **/
    protected $reasons = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        210 => 'Content Different',
        226 => 'IM Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => null,
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirects',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'recondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable for legal reasons',
        456 => 'Unrecoverable Error',
        499 => 'client has closed connection',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant also negociate',
        507 => 'Insufficient storage',
        508 => 'Loop detected',
        509 => 'Bandwith Limit Exceeded',
        510 => 'Not extended',
        511 => 'Network authentication required',
        520 => 'Web server is returning an unknown error',
        521 => 'The server is not available " for legal reasons "',
    ];

    abstract public function respond();

    public function makeHeaders()
    {
        // Make status header
        header("{$_SERVER['SERVER_PROTOCOL']} {$this->status} {$this->reasons[$this->status]}");
        // The primitive headers modified by the user will be overridden 
        $headers = array_merge($this->primitiveHeaders, $this->userHeaders);
        foreach ($headers as $header => $value) {
            header("$header: $value");
        }
    }

    /**
     * @param array $headers
     */
    public function addHeaders($headers)
    {
        foreach ($headers as $header => $value) {
            $this->userHeaders[$header] = $value;
        }
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function addHeader($header, $value)
    {
        $this->userHeaders[$header] = $value;
    }

    /**
     * @param string $header
     */
    public function removeHeader($header)
    {
        unset($this->userHeaders[$header]);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->userHeaders;
    }
}
