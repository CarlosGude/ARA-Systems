<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use App\Form\PurchaseLineType;
use App\Security\Voter\AbstractVoter;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("management/purchase-", name="purchase_")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("update-{type}-to-{line}/{value}", name="update_line")
     */
    public function index(
        PurchaseLine $line,
        string $type,
        float $value,
        EntityManagerInterface $em,
        Request $request,
        TranslatorInterface $translator
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('UPDATE', $line);

        if(!in_array($type,['price','quantity'],true)){
            throw new RuntimeException('The value of type '.$type.' is not valid.');
        }

        if($type === 'quantity'){
            $line->setQuantity($value);
        }

        if($type === 'price'){
            $line->setPrice($value);
        }

        $this->addFlash('success', $translator->trans('purchaseLine.added', ['{{product}}' => $line->getProduct()]));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("change-{status}/{purchase}", name="change_status")
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

    /**
     * @Route("create-purchase-line/{purchase}", name="add_line")
     */
    public function addLine(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        Purchase $purchase
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted(AbstractVoter::CREATE, $line = new PurchaseLine());

        $line
            ->setUser($user)
            ->setPurchase($purchase)
            ->setCompany($user->getCompany());

        $form = $this->createForm(PurchaseLineType::class, $line);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($line);
            $em->flush();

            $this->addFlash('success', $translator->trans('purchaseLine.created', ['{{element}}' => $line]));

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('front/create/purchaseLine.html.twig', [
            'purchase' => $purchase,
            'form' => $form->createView(),
        ]);
    }
}
