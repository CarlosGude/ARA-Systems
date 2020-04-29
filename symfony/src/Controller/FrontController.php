<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use App\Interfaces\EntityInterface;
use App\Interfaces\ImageInterface;
use App\Security\AbstractUserRoles;
use App\Security\Voter\AbstractVoter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
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
 * Class ManagementController.
 *
 * @Route("management",name="front_")
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
     * @Route("/create/{entity}", name="create")
     */
    public function create(
        string $entity,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);
        $formClass = self::FORM_NAMESPACE.ucfirst($entity).'Type';

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!class_exists($formClass)) {
            throw new NotFoundHttpException('The form not exists.');
        }

        $element = new $class();

        if ($this->isAValidEntity($class)) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted(AbstractVoter::CREATE, $element);

        if (property_exists($element, 'company')) {
            $element->setCompany($user->getCompany());
        }

        if (property_exists($element, 'user')) {
            $element->setUser($user);
        }

        $form = $this->createForm($formClass, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($element);
            $em->flush();

            $this->addFlash('success', $translator->trans($entity.'.created', ['{{element}}' => $element]));

            return $this->redirectToRoute('front_edit', ['entity' => $entity, 'id' => $element->getId()]);
        }

        return $this->render('front/create/'.$entity.'.html.twig', [
            'action' => 'create',
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{entity}/{id}", name="edit")
     */
    public function edit(
        string $entity,
        string $id,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);
        $formClass = self::FORM_NAMESPACE.ucfirst($entity).'Type';

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

        if ($this->isAValidEntity($class)) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted(AbstractVoter::UPDATE, $element);

        $form = $this->createForm($formClass, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $translator->trans($entity.'.edited', ['{{element}}' => $element]));
            return $this->redirectToRoute('front_edit', ['entity' => $entity, 'id' => $element->getId()]);
        }

        return $this->render('front/edit/'.$entity.'.html.twig', [
            'action' => 'edit',
            'element' => $element,
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/{entity}/{sort}/{direction}/{page}", name="list")
     */
    public function list(
        string $entity,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        string $sort = 'name',
        string $direction = 'asc',
        int $page = 1
    ): Response {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);
        /** @var User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted(AbstractVoter::READ, new $class());

        if ($this->isAValidEntity(new $class())) {
            throw new RuntimeException('The class is not valid.');
        }

        $data = ($this->isGranted(AbstractUserRoles::ROLE_GOD))
            ? $em->getRepository($class)->findBy([], [$sort => strtoupper($direction)])
            : $em->getRepository($class)->findBy(['company' => $user->getCompany()], [$sort => strtoupper($direction)]);

        $pagination = $paginator->paginate($data, $page, 10);

        return $this->render('front/list/'.$entity.'.html.twig', [
            'action' => 'list',
            'pagination' => $pagination,
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/delete/{entity}/{id}", name="delete")
     */
    public function delete(string $entity, string $id, Request $request, EntityManagerInterface $em): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new RuntimeException('Page not found.');
        }

        if ($this->isAValidEntity($class)) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted(AbstractVoter::DELETE, $element);

        $em->remove($element);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param string $entity
     * @param string $id
     * @param EntityManager $em
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws OptimisticLockException
     *  @Route("/remove-image/{entity}/{id}", name="delete_image")
     */
    public function removeImage(string $entity,string $id, EntityManagerInterface $em, Request $request): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new RuntimeException('Page not found.');
        }

        if (!$element instanceof ImageInterface || $this->isAValidEntity($class)) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted(AbstractVoter::DELETE, $element);

        $element->setImage(null);

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $class
     */
    protected function isAValidEntity($class): bool
    {
        if (!is_object($class)) {
            return false;
        }

        if (!$class instanceof UserInterface) {
            return false;
        }

        if (!$class instanceof EntityInterface) {
            return false;
        }

        if (!$class instanceof Company) {
            return false;
        }

        return true;
    }
}
