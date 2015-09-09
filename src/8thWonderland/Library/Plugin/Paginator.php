<?php

namespace Wonderland\Library\Plugin;

/**
 * plugin permettant la pagination de résultats
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/
class Paginator {
    protected $_ItemsperPage = 10;                                              // Nb d'éléments par page (par défaut)
    protected $_PageRange = 5;                                                  // Nb pages par saut
    protected $_datas;                                                          // Jeu de résultat de données
    protected $_CurrentPage = 1;                                                // Page courante
    
    
    public function __construct($datas, $options=null) 
    {
        if (isset($options)) {
            $options = array_change_key_case($options, CASE_LOWER);
            if (isset($options["itemperpage"]) && intval($options["itemperpage"]))      {   $this->_ItemperPage = $options["itemperpage"];  }
            if (isset($options["pagerange"]) && intval($options["pagerange"]))          {   $this->_PageRange = $options["pagerange"];      }
        }
        
        $this->_datas = $datas;
    }
    
    
    // Nombre total de pages
    // =====================
    public function getNumPage()
    {
        if ($this->_ItemsperPage == 0)       {   return -1;  }
        if (count($this->_datas) == 0)      {   return 0;   }
        return (intval(count($this->_datas) / $this->_ItemsperPage) + 1);
    }
    
    
    // Nombre total d'items
    // ====================
    public function getItems()
    {
        return count($this->_datas);
    }
        
    
    // Nb items par page
    // =================
    public function _setItemsPage($value)
    {
        if (intval($value))     {   $this->_ItemsperPage = $value;  }
    }
    public function getItemsPage()
    {
        return $this->_ItemsperPage;
    }
    
    
    // Page courante
    // =============
    public function setCurrentPage($value)
    {
        if ($value >1 && $value < $this->getNumPage()+1)     {   $this->_CurrentPage = $value;   }
    }
    public function getCurrentPage()
    {
        return $this->_CurrentPage;
    }
        
    
    // Page range
    // ==========
    public function getPageRange()
    {
        return $this->_PageRange;
    }
    
    
    // Renvoi des items de la page courante
    // ====================================
    public function getCurrentItems()
    {
        $nFirstItem = (($this->_CurrentPage - 1) * $this->_ItemsperPage);
        $nLastItem = ($this->_CurrentPage * $this->_ItemsperPage) -1;
        if ($nLastItem >= $this->getItems())    {   $nLastItem = $this->getItems()-1;    }
        
        $result = array();
        for ($i=$nFirstItem; $i<=$nLastItem; $i++) {
            $result[$i-$nFirstItem] = $this->_datas[$i];
        }
        
        return $result;
    }
    
    
    // Affichage de la page suivante
    // =============================
    public function NextPage() 
    {
        if ($this->_CurrentPage < $this->getNumPage())     {   $this->_CurrentPage = $this->_CurrentPage +1;   }
        return ($this->getCurrentItems());
    }
        
    
    // Affichage de la page précédente
    // ===============================
    public function PreciousPage() 
    {
        if ($this->_CurrentPage > 1)    {   $this->_CurrentPage = $this->_CurrentPage -1;   }
        return ($this->getCurrentItems());
    }
} 
?>
