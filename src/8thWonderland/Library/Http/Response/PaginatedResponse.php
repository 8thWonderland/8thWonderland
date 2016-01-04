<?php

namespace Wonderland\Library\Http\Response;

class PaginatedResponse extends AbstractResponse
{
    /** @var string **/
    protected $location;
    /** @var string **/
    protected $rangeUnit;
    /** @var string **/
    protected $contentRange;

    /**
     * @param mixed  $data
     * @param string $rangeUnit
     * @param string $range
     * @param int    $maxElements
     * @param int    $status
     */
    public function __construct($data = '', $rangeUnit, $range, $maxElements, $status = 200)
    {
        $this->data = $data;
        $this->status = $status;

        $this->location = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
        $this->rangeUnit = $rangeUnit;
        $this->contentRange = "$range/$maxElements";

        $this->generatePaginationHeaders();
    }

    public function generatePaginationHeaders()
    {
        $data = explode('/', $this->contentRange);
        $ranges = explode('-', $data[0]);
        $minRange = $ranges[0];
        $maxRange = $ranges[1];
        $maxItems = $data[1];
        $limit = $maxRange - $minRange;

        $links = [
            'first' => "0-{$limit}",
            'previous' => ($minRange - $limit).'-'.$minRange,
            'next' => $maxRange.'-'.($maxRange + $limit),
            'last' => $maxItems - $limit.'-'.$maxItems,
        ];
        // Avoid to set next link with smaller range than the min range
        if ($minRange < $limit) {
            $links['previous'] = $links['first'];
        }
        // Avoid to set next link with bigger range than the max range
        if ($maxRange + $limit > $maxItems) {
            $links['next'] = $links['last'];
        }
        $this->addHeaders([
            'Accept-Ranges' => $this->rangeUnit,
            'Content-Range' => $this->contentRange,
            'Link' => "<{$this->location}>; rel=\"first\"; items=\"{$links['first']}\",".
                "<{$this->location}>; rel=\"previous\"; items=\"{$links['previous']}\",".
                "<{$this->location}>; rel=\"next\"; items=\"{$links['next']}\",".
                "<{$this->location}>; rel=\"last\"; items=\"{$links['last']}\"",
            'Access-Control-Expose-Headers' => 'Accept-Ranges, Content-Range',
        ]);
    }

    public function respond()
    {
        echo json_encode($this->data, $this->status);
    }
}
