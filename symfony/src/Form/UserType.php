<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['data'];

        if($user->getId()){
            $builder
                ->add('name',TextType::class,['label' => 'user.name'])
                ->add('email',EmailType::class,['label' => 'user.email'])
            ;
        }else{
            $builder
                ->add('name',TextType::class,['label' => 'user.name'])
                ->add('email',EmailType::class,['label' => 'user.email'])
                ->add('password',PasswordType::class,['label'=>'user.password'])
            ;
        }

        $builder->add('submit',SubmitType::class,['label' => 'save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
