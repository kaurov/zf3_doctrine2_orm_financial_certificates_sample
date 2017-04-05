<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * This is the main controller class of the Blog application. The 
 * controller class is used to receive user input,  
 * pass the data to the models and pass the results returned by models to the 
 * view for rendering.
 */
class IndexController extends AbstractActionController
{

    /**
     * This is the default "index" action of the controller. It displays the 
     * Recent Certificates page containing the recent blog certificates.
     */
    public function indexAction()
    {
        $appName = 'Certificates Sample';
        $appDescription = 'A simple sample application for the Using Zend Framework 3 + Derivatives Certificates';

        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }

    /**
     * This action displays the About page.
     */
    public function aboutAction()
    {
        $appName = 'Certificates Sample';
        $appDescription = 'A simple sample application for the Using Zend Framework 3 + Derivatives Certificates';

        return new ViewModel([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }

}
