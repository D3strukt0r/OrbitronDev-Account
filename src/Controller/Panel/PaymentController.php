<?php

namespace App\Controller\Panel;

use App\Entity\User;
use App\Entity\UserPaymentMethods;
use App\Form\AddPaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    public static function __setupNavigation()
    {
        return [
            [
                'type' => 'group',
                'parent' => 'root',
                'id' => 'payment',
                'title' => 'Billing',
                'icon' => 'hs-admin-credit-card',
            ],
            [
                'type' => 'link',
                'parent' => 'payment',
                'id' => 'payment_methods',
                'title' => 'Payment methods',
                'href' => 'payment',
                'view' => 'PaymentController::payment',
            ],
            [
                'type' => 'link',
                'parent' => 'payment',
                'id' => 'add_payment_method',
                'title' => null,
                'display' => false,
                'href' => 'add-payment-method',
                'view' => 'PaymentController::addMethod',
            ],
            [
                'type' => 'link',
                'parent' => 'payment',
                'id' => 'remove_payment_method',
                'title' => null,
                'display' => false,
                'href' => 'remove-payment-method',
                'view' => 'PaymentController::removeMethod',
            ],
        ];
    }

    public static function __callNumber()
    {
        return 20;
    }

    public function payment($navigation)
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            'panel/payment_methods.html.twig',
            [
                'navigation_links' => $navigation,
                'payment_methods' => $user->getPaymentMethods(),
            ]
        );
    }

    public function addMethod(Request $request, $navigation)
    {
        $form = $this->createForm(AddPaymentMethod::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            /** @var User $user */
            $user = $this->getUser();
            $obj = (new UserPaymentMethods())
                ->setUser($user)
                ->setType($formData['type'])
                ->setData(json_decode($formData['data'], true))
            ;
            $user->addPaymentMethod($obj);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('add_payment_method', 'Successfully added payment method');
        }

        return $this->render(
            'panel/add_payment_methods.html.twig',
            [
                'navigation_links' => $navigation,
                'form' => $form->createView(),
            ]
        );
    }
}
