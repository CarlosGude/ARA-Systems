<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontController
 * @package App\Controller
 * @Route(name="front_")
 */
class FrontController extends AbstractController
{
    public const ENTITY_NAMESPACE = 'App\Entity\\';

    /**
     * @Route("/list/{entity}", name="list")
     * @param string $entity
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function list(
        string $entity,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        Request $request
    ): Response {

        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        /** @var User $user */
        $user = $this->getUser();

        $pagination = $paginator->paginate(
            $em->getRepository($class)->findBy(['company' => $user->getCompany()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('front/list.html.twig', ['entity' => $entity, 'pagination' => $pagination]);
    }

    /**
     * @param string $entity
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/create/{entity}", name="create")
     */
    public function create(string $entity, Request $request, EntityManagerInterface $em): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = new $class();

        $this->denyAccessUnlessGranted('CREATED', $element);

        /** @var User $user */
        $user = $this->getUser();

        $element->setUser($user)->setCompany($user->getCompany());

        $form = $this->createForm(CategoryType::class,$element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($element);
            $em->flush();

            return $this->redirectToRoute('front_edit',['entity' => $entity,'id' => $element->getId()]);
        }

        return $this->render('front/form.html.twig', ['entity' => $entity,'form' => $form->createView()]);
    }

    /**
     * @param string $entity
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param string $id
     * @return Response
     * @Route("/edit/{entity}/{id}", name="edit")
     */
    public function edit(string $entity, Request $request, EntityManagerInterface $em, string $id): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new NotFoundHttpException('Page not found.');
        }

        $this->denyAccessUnlessGranted('UPDATE', $element);

        $form = $this->createForm(CategoryType::class,$element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('front_list',['entity' => $entity]);
        }

        return $this->render('front/form.html.twig', ['entity' => $entity,'form' => $form->createView()]);
    }

    /**
     * @param string $entity
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param string $id
     * @return Response
     * @Route("/remove/{entity}/{id}", name="remove")
     */
    public function remove(string $entity, Request $request, EntityManagerInterface $em, string $id): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new NotFoundHttpException('Page not found.');
        }

        $this->denyAccessUnlessGranted('DELETE', $element);

        $em->remove($element);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
