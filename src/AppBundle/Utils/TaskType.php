<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 11/11/16
 * Time: 6:39 PM
 */

namespace AppBundle\Utils;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $usernameIdPairs = $options["usernameIdPairs"];

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('userId', ChoiceType::class, [
                    'choices' => $usernameIdPairs,
                    'group_by' => function($category, $key, $index){
                        return null;
                    },
                    'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task',
            'usernameIdPairs' => null
        ));
    }
}