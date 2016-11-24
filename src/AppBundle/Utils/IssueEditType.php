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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => [
                    "NEW" => IssueState::NEWBE,
                    "ACKNOWLEDGED" => IssueState::ACK,
                    "CONFIRMED" => IssueState::CONF,
                    "ASSIGNED" => IssueState::ASSIGNED,
                    "CLOSED" => IssueState::CLOSED
                ],
                'group_by' => function($category, $key, $index){
                    return null;
                },
                'required' => true
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    "LOW" => IssuePriority::LOW,
                    "NORMAL" => IssuePriority::NORMAL,
                    "HIGH" => IssuePriority::HIGH
                ],
                'group_by' => function($category, $key, $index){
                    return null;
                },
                'required' => true
            ])
            ->add('comment', TextareaType::class, ["required" => false]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Issue',
        ));
    }
}