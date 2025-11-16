<?php
/**
 * React SPA Controller
 *
 * Serves the React frontend application built with Vite.
 */
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * ReactController
 *
 * Controller for serving the React SPA frontend.
 * This allows the React application to use client-side routing
 * while being served through Laminas MVC.
 */
class ReactController extends AbstractActionController
{
    /**
     * Index action - serves the React SPA
     *
     * All routes under /react/* are handled by this action,
     * which serves the React app's index.html. React Router
     * then handles the client-side routing.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        // Disable the default layout - React app has its own layout
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
