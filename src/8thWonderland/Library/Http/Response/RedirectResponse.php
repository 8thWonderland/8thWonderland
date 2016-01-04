<?php

namespace Wonderland\Library\Http\Response;

class RedirectResponse extends AbstractResponse
{
    /**
     * @param string $url
     * @param int    $status
     * @param string $protocol
     */
    public function __construct($url, $status = 300, $protocol = 'http')
    {
        $this->status = $status;
        $this->addHeader('Location', "$protocol://{$_SERVER['SERVER_NAME']}".VIEW_PATH.$url);
    }

    public function respond()
    {
    }
}
