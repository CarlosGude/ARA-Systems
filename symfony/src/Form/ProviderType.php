<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Provider;
use App\Entity\User;
use App\EventSubscriber\ImageFormSubscriber;
use App\Security\AbstractUserRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Image;

class ProviderType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Provider $provider */
        $provider = $options['data'];
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$provider->getId() && in_array(AbstractUserRoles::ROLE_GOD, $user->getRoles(), true)) {
            $builder->add('company',EntityType::class,['label'=> 'company.label','class'=>Company::class]);
        }

        $builder
            ->add('name', TextType::class, ['label' => 'provider.name'])
            ->add('image', FileType::class, [
                'label' => 'provider.image',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image()],
            ])
            ->add('email', EmailType::class, ['label' => 'provider.email'])
            ->add('description', TextareaType::class, ['label' => 'provider.description', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->addEventListener(FormEvents::POST_SUBMIT, [ImageFormSubscriber::class, 'postSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}
