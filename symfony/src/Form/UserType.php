<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'];

        $profiles = User::getProfiles();

        unset($profiles[User::ROLE_GOD]);

        $builder
            ->add('name', TextType::class, ['label' => 'user.name'])
            ->add('profile',ChoiceType::class,[
                'label'=>'user.profile',
                'choices'=> $profiles,
                'placeholder'=> 'user.profileChoose'
            ])
            ->add('email', EmailType::class, ['label' => 'user.email'])
        ;

        if (!$user->getId()) {
            $builder
                ->add('password', PasswordType::class, ['label' => 'user.password'])
            ;
        }

        $builder->add('submit', SubmitType::class, ['label' => 'save']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
