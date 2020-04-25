<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("management/purchase", name="purchase_")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/purchase-line/{line}/{quantity}", name="update_quantity")
     */
    public function index(
        PurchaseLine $line,
        int $quantity,
        EntityManagerInterface $em,
        Request $request,
        TranslatorInterface $translator
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('UPDATE', $line);

        $line->setQuantity($quantity);

        $this->addFlash('success', $translator->trans('purchaseLine.added', ['{{product}}' => $line->getProduct()]));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("purchase/change-status/{purchase}/{status}", name="change_status")
     */
    public function changeStatus(
        Purchase $purchase,
        string $status,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Request $request
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('UPDATE', $purchase);
        $purchase->setStatus($status);

        $this->addFlash('success', $translator->trans('purchase.update', [
            '{{reference}}' => $purchase->getReference(),
            '{{status}}' => $purchase->getStatus(),
        ]));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
