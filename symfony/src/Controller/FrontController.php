<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use App\Interfaces\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ManagementController
 * @package App\Controller
 * @Route(name="front_")
 */
class FrontController extends AbstractController
{
    public const ENTITY_NAMESPACE = 'App\Entity\\';
    public const FORM_NAMESPACE = 'App\Form\\';

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {

        return $this->render('front/dashboard.html.twig', []);
    }

    /**
     * @Route("/create/{entity}/{purchase}", name="create")
     * @param string $entity
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param string|null $purchase
     * @return Response
     */
    public function create(
        string $entity,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $purchase = null
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $class = self::ENTITY_NAMESPACE . ucfirst($entity);
        $formClass = self::FORM_NAMESPACE . ucfirst($entity) . 'Type';

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!class_exists($formClass)) {
            throw new NotFoundHttpException('The form not exists.');
        }

        $element = new $class();

        if (!$element instanceof EntityInterface && !$element instanceof UserInterface) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted('CREATE', $element);

        $element->setCompany($user->getCompany());

        if(property_exists($element,'user')) {
            $element->setUser($user);
        }

        if ($purchase && $element instanceof PurchaseLine){
            $purchase = $em->getRepository(Purchase::class)->find($purchase);
            $element->setPurchase($purchase);
        }

        $form = $this->createForm($formClass, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($element);
            $em->flush();

            $this->addFlash('success',$translator->trans($entity.'.created',['{{element}}' =>$element]));

            return ($purchase)
                ? $this->redirect($request->headers->get('referer'))
                : $this->redirectToRoute('front_edit', ['entity' => $entity, 'id' => $element->getId()]);
        }

        return $this->render('front/create/' . $entity . '.html.twig', [
            'action' => 'create',
            'purchase'=> $purchase,
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{entity}/{id}", name="edit")
     * @param string $entity
     * @param string $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(
        string $entity,
        string $id,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        $class = self::ENTITY_NAMESPACE . ucfirst($entity);
        $formClass = self::FORM_NAMESPACE . ucfirst($entity) . 'Type';

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!class_exists($formClass)) {
            throw new NotFoundHttpException('The form not exists.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new RuntimeException('Page not found.');
        }

        if (!$element instanceof EntityInterface && !$element instanceof UserInterface) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted('EDIT', $element);

        $form = $this->createForm($formClass, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('success',$translator->trans($entity.'.edited',['{{element}}' =>$element]));
            return $this->redirectToRoute('front_edit', ['entity' => $entity, 'id' => $element->getId()]);
        }

        return $this->render('front/edit/' . $entity . '.html.twig', [
            'action' => 'edit',
            'element' => $element,
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/list/{entity}/{sort}/{direction}/{page}", name="list")
     * @param string $entity
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param string $sort
     * @param string $direction
     * @param int $page
     * @return Response
     */
    public function list(
        string $entity,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        string $sort = 'name',
        string $direction = 'asc',
        int $page = 1
    ): Response
    {
        $class = self::ENTITY_NAMESPACE . ucfirst($entity);
        /** @var User $user */
        $user = $this->getUser();

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!new $class() instanceof EntityInterface && !new $class() instanceof UserInterface) {
            throw new RuntimeException('The class is not valid.');
        }

        $pagination = $paginator->paginate(
            $em->getRepository($class)->findBy(['company' => $user->getCompany()],[$sort=>$direction]), /* query NOT result */
            $page, /*page number*/
            10 /*limit per page*/
        );

        return $this->render('front/list/' . $entity . '.html.twig', [
            'action' => 'list',
            'pagination' => $pagination,
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/delete/{entity}/{id}", name="delete")
     * @param string $entity
     * @param string $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(string $entity, string $id, Request $request, EntityManagerInterface $em): Response
    {
        $class = self::ENTITY_NAMESPACE . ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new RuntimeException('Page not found.');
        }

        $em->remove($element);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
