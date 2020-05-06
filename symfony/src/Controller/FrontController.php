<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Product;
use App\Entity\ProductMediaObject;
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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use setasign\Fpdi\Fpdi;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
     * @Route("/dashboard/{company}", name="dashboard")
     */
    public function index(Company $company = null): Response
    {
        if(!$company && in_array(AbstractUserRoles::ROLE_GOD, $this->getUser()->getRoles(), true)){
            $company = $this->getUser()->getCompany();
        }
        if(!in_array(AbstractUserRoles::ROLE_GOD, $this->getUser()->getRoles(), true)){
            $company = $this->getUser()->getCompany();
        }

        return $this->render('front/dashboard.html.twig', ['company'=> $company]);
    }

    /**
     * @Route("/list/{entity}/{view}/{sort}/{direction}/{page}/{company}", name="list")
     */
    public function list(
        string $entity,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        string $view = 'list',
        string $sort = 'name',
        string $direction = 'asc',
        int $page = 1,
        Company $company = null
    ): Response {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);
        /** @var User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted(AbstractVoter::READ, new $class());

        if ($this->isAValidEntity(new $class())) {
            throw new RuntimeException('The class is not valid.');
        }

        $filter = ($class === Company::class) ? 'findAll' : 'findBy';

        $data = ($this->isGranted(AbstractUserRoles::ROLE_GOD))
            ? $em->getRepository($class)->$filter(['company' => $company ?? $user->getCompany()], [$sort => strtoupper($direction)])
            : $em->getRepository($class)->findBy(['company' => $user->getCompany()], [$sort => strtoupper($direction)]);

        $pagination = $paginator->paginate($data, $page, 10);

        return $this->render('front/'.$view.'/'.$entity.'.html.twig', [
            'action' => 'list',
            'pagination' => $pagination,
            'entity' => $entity,
            'company' => $company ?? $user->getCompany(),
        ]);
    }

    /**
     * @param EntityManager $em
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws OptimisticLockException
     * @Route("/remove-image/{entity}/{id}", name="delete_image")
     */
    public function removeImage(string $entity, string $id, EntityManagerInterface $em, Request $request): Response
    {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);

        if (!class_exists($class)) {
            throw new NotFoundHttpException('Page not found.');
        }

        $element = $em->getRepository($class)->find($id);

        if (!$element) {
            throw new RuntimeException('Page not found.');
        }

        if ( !$element instanceof ImageInterface && !$element instanceof ProductMediaObject) {
            throw new RuntimeException('The class is not valid.');
        }

        $this->denyAccessUnlessGranted(AbstractVoter::DELETE, $element);

        if($element instanceof ProductMediaObject){
            $em->remove($element);
        }else{
            $element->setImage(null);
        }
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/delete-{entity}/{id}", name="delete")
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
     * @Route("/create/{entity}/{company}", name="create")
     */
    public function create(
        string $entity,
        Company $company = null,
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

        if(!$element instanceof Company && $company && in_array(AbstractUserRoles::ROLE_GOD, $user->getRoles(), true)) {
            $element->setCompany($company);
        }

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

            return $this->redirectToRoute('front_edit', [
                'entity' => $entity,
                'id' => $element->getId(),
                'company' => $company ?? $user->getCompany(),
                ]);
        }

        return $this->render('front/create/'.$entity.'.html.twig', [
            'action' => 'create',
            'entity' => $entity,
            'form' => $form->createView(),
            'company' => $company ?? $user->getCompany()
        ]);
    }

    /**
     * @Route("/{view}/{entity}/{id}/{company}", name="edit")
     */
    public function edit(
        string $view = 'edit',
        Company $company = null,
        string $entity,
        string $id,
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response {
        $class = self::ENTITY_NAMESPACE.ucfirst($entity);
        $formClass = self::FORM_NAMESPACE.ucfirst($entity).'Type';
        /** @var User $user */
        $user = $this->getUser();

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
            return $this->redirectToRoute('front_edit', [
                'entity' => $entity,
                'id' => $element->getId(),
                'company' => $company ?? $user->getCompany(),
            ]);
        }

        return $this->render('front/'.$view.'/'.$entity.'.html.twig', [
            'action' => 'edit',
            'element' => $element,
            'entity' => $entity,
            'form' => $form->createView(),
            'company' => $company ?? $user->getCompany(),
        ]);
    }

    /**
     * @Route("/create-pdf-for-product/{product}", name="create_pdf")
     */
    public function createPdfWitProduct(Product $product, KernelInterface $kernel,Request $request, UploaderHelper $helper)
    {
        $pdf = new Fpdi('L','mm', array(54,85.6));

        $pdf->AddPage();

        $pdf->setSourceFile($kernel->getProjectDir().'/product.pdf');

        $templateId = $pdf->importPage(1);

        $pdf->useTemplate($templateId, ['adjustPageSize' => true]);
        $pdf->SetFont('Helvetica');
        $pdf->SetTextColor(0, 0, 0);

        //Company name
        $pdf->SetXY(15, 15);
        if ($product->getCompany()->getImage()){
            $file = $kernel->getProjectDir().'/public/'.$helper->asset($product->getCompany()->getImage(), 'file');
            $pdf->Image($file,12,10,70,30);
        }else{
            $pdf->Write(0, utf8_decode($product->getCompany()->getName()));
        }

        if ($product->getImage()){
            $file = $kernel->getProjectDir().'/public/'.$helper->asset($product->getImage(), 'file');
            $pdf->Image($file,16,60,60,60);
        }

        //Company Info
        $pdf->SetXY(140, 15);
        $pdf->Write(0, utf8_decode($product->getCompany()->getName()));
        $pdf->SetXY(140, 20);
        $pdf->Write(0, utf8_decode($product->getCompany()->getCif()));
        $pdf->SetXY(140, 25);
        $pdf->Write(0, utf8_decode($product->getCompany()->getAddress()));
        $pdf->SetXY(140, 30);
        $pdf->Write(0, utf8_decode($product->getCompany()->getPhone()));
        $pdf->SetXY(140, 35);
        $pdf->Write(0, utf8_decode($product->getCompany()->getEmail()));

        $pdf->SetXY(135, 50);
        $pdf->Write(0, utf8_decode($product->getName()));

        $pdf->SetXY(135, 63);
        $pdf->Write(0, utf8_decode($product->getReference()));

        $pdf->SetXY(130, 75);
        $pdf->Write(0,utf8_decode( $product->getDescription()));

        $pdf->SetXY(135, 120);
        $pdf->Write(0, $product->getKilograms());

        $pdf->SetXY(135, 127);
        $pdf->Write(0, $product->getProductLength());

        $pdf->SetXY(155, 127);
        $pdf->Write(0, $product->getProductWidth());

        $pdf->SetXY(170, 127);
        $pdf->Write(0, $product->getProductHeight());

        $pdf->Output('',$product->getSlug().'.pdf',true);

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
