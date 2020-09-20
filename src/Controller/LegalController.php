<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    /**
     * @Route("/terms-of-service", name="tos")
     */
    public function tos()
    {
        throw $this->createNotFoundException('Work in progress');
    }

    /**
     * @Route("/privacy-policy", name="privacy")
     */
    public function privacy()
    {
        throw $this->createNotFoundException('Work in progress');
    }
}
