<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class IndexController extends ActionController
{
    /**
     * @return \Wonderland\Library\Http\Response\Response
     */
    public function indexAction()
    {
        // controle si l'utilisateur est déjà connecté
        if ($this->getUser() !== null) {
            return $this->redirect('intranet/index');
        }

        return $this->render('accueil', [
            'countries' => $this->application->get('country_manager')->getCountries(),
        ]);
    }
    
    public function motionsAction() {
        $range = $this->request->getRange(15);
        
        $motionManager = $this->application->get('motion_manager');
        
        $motions = $motionManager->getMotions($range['min'], $range['max']);
        $nbMotions = $motionManager->countMotions();
        
        if ($this->is_Ajax()) {
            return new PaginatedResponse([
                    'motions' => $motions,
                    'total_motions' => $nbMotions,
                ],
                $_SERVER['HTTP_RANGE_UNIT'],
                $_SERVER['HTTP_RANGE'],
                $nbMotions
            );
        }
        return $this->render('motions/public_list', [
            'motions' => $motions,
            'motions_range' => $range['max'],
            'total_motions' => $nbMotions
        ]);
    }
    
    public function motionAction() {
        return $this->render('motions/public_show', [
            'motion' => $this->application->get('motion_manager')->getMotion($this->request->get('motion_id', null, 'int'))
        ]);
    }
}
