<?php

namespace Wonderland\Library\Controller;

/**
 * Classe abstraite fournissant les fonction de base d'un controleur Action
 *
 * @author Brennan
 */
abstract class ActionController {
    protected $_directoryViews = 'src/8thWonderland/Application/views';                   // Chemin par défaut des vues
    protected $_directoryControllers = 'src/8thWonderland/Application/Controller';       // Chemin par défaut des controleurs
    protected $_view = array();                                         // Liste des valeurs transmises à la vue
    protected $_suffix = ".view";                                       // Suffixe des fichiers de vues
    
    
    // Appel du fichier de vue
    // =======================
    public function render($action)
    {
        $filename = $this->_directoryViews . "/" . $action . $this->_suffix;
        if (!file_exists($filename))    {   throw new \Exception("The view '" . $filename . "' not found !");    }
        
        include $filename;
    }
    
    
    // Affichage d'un texte directement
    // ================================
    public function display($msg)
    {
        echo $msg;
    }
    
    
    // Exécute une redirection
    // =======================
    public function redirect($url)
    {   
        if ($this->is_Ajax()) {
            echo json_encode(array("status" => 1, "reponse" => $url));
        } else {
            $params = $this->_formatURL($url);

            // route vers le controleur et l'action demandée
            $ctrl = new $params[0]();
            $action = $params[1] . "Action";

            $ctrl->$action();
        }
        
        exit();
    }
    
    protected function is_Ajax()
    {
        return (array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    
    /**
     * @param string $url
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function _formatURL($url)
    {
        $action = 'index';
        if ($url[0] != '/') {
            $url = '/'.$url;
        }
        $params = explode('/', $url);
        if (count($params) < 2) {
            throw new \InvalidArgumentException('The url is invalid !');
        }
        $controller = "Wonderland\\Application\\Controller\\{$params[1]}Controller";
        if (isset($params[2])) {
            $action = $params[2];
        }
        
        // Vérification si le controleur existe
        $filename = "{$this->_directoryControllers}/{$params[1]}Controller.php";
        if (!file_exists($filename)) {
            throw new \Exception("The ActionController '" . $controller . "' does not exist !");
        }
        // Vérification si l'action existe
        if (!in_array($action . "Action", get_class_methods($controller))) {
            throw new \Exception("The Action '" . $action . "' does not exist !");
        }
        
        return array($controller, $action);
    }
}