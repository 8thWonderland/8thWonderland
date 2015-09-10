<?php

namespace Wonderland\Library\Plugin;

use Wonderland\Library\Application;

class Paginator {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    /** @var int **/
    protected $itemsPerPage = 10;
    /** @var int **/
    protected $pageRange = 5;
    /** @var array **/
    protected $data;
    /** @var int **/
    protected $currentPage = 1;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param array $data
     */
    public function setData(array $data) {
        $this->data = $data;
    }
    
    /**
     * @return int
     */
    public function getNumPage() {
        if ($this->itemsPerPage === 0) {
            return -1;
        }
        if (count($this->data) === 0) {
            return 0;
        }
        return (intval(count($this->data) / $this->itemsPerPage) + 1);
    }
    
    /**
     * @return int
     */
    public function countItems() {
        return count($this->data);
    }
        
    /**
     * @param int $value
     * @return \Wonderland\Library\Plugin\Paginator
     * @throws \InvalidArgumentException
     */
    public function setItemsPerPage($value) {
        if (!is_int($value)) {
            throw new \InvalidArgumentException('The items per page value must be an integer');
        }
        $this->itemsPerPage = $value;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    
    /**
     * @param int $value
     * @return \Wonderland\Library\Plugin\Paginator
     */
    public function setCurrentPage($value) {
        if ($value > 1 && $value < $this->getNumPage() + 1) {
            $this->currentPage = $value;
        }
        return $this;
    }
    
    /**
     * @return int
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
        
    /**
     * @param int $pageRange
     * @return \Wonderland\Library\Plugin\Paginator
     * @throws \InvalidArgumentException
     */
    public function setPageRange($pageRange) {
        if(!is_int($pageRange)) {
            throw new \InvalidArgumentException('The page range must be an integer');
        }
        $this->pageRange = $pageRange;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPageRange() {
        return $this->pageRange;
    }
    
    /**
     * @return array
     */
    public function getCurrentItems() {
        $nFirstItem = (($this->currentPage - 1) * $this->itemsPerPage);
        $nLastItem = ($this->currentPage * $this->itemsPerPage) -1;
        
        if ($nLastItem >= $this->countItems()) {
            $nLastItem = $this->countItems() - 1;
        }
        
        $result = [];
        for ($i = $nFirstItem; $i <= $nLastItem; ++$i) {
            $result[$i-$nFirstItem] = $this->data[$i];
        }
        return $result;
    }
    
    /**
     * @return array
     */
    public function NextPage() {
        if ($this->currentPage < $this->getNumPage()) {
            ++$this->currentPage;
        }
        return $this->getCurrentItems();
    }
        
    /**
     * @return array
     */
    public function PreciousPage() {
        if ($this->currentPage > 1) {
            --$this->currentPage;
        }
        return $this->getCurrentItems();
    }
}
