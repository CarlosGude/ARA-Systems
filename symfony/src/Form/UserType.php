<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use App\EventSubscriber\ImageFormSubscriber;
use App\Security\AbstractUserRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('name', TextType::class, ['label' => 'user.name'])
            ->add('profile', ChoiceType::class, [
                'label' => 'user.profile',
                'choices' => $this->getProfiles(),
                'placeholder' => 'user.profileChoose',
            ])
            ->add('email', EmailType::class, ['label' => 'user.email'])
            ->add('image', FileType::class, [
                'label' => 'user.avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [new Image()],
            ])
        ;

        if (!$user->getId()) {
            $builder
                ->add('password', PasswordType::class, ['label' => 'user.password'])
            ;
        }

        $builder
            ->add('submit', SubmitType::class, ['label' => 'save'])
            ->addEventListener(FormEvents::POST_SUBMIT, [ImageFormSubscriber::class, 'postSubmit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    protected function getProfiles()
    {
        return [
            User::PROFILE_ADMIN => User::PROFILE_ADMIN,
            User::PROFILE_SELLER => User::PROFILE_SELLER,
            User::PROFILE_PURCHASER => User::PROFILE_PURCHASER,
        ];
    }
}
