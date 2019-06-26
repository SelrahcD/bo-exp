<?php
namespace App\Form;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateCalendarFilterType extends FilterType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'choices' => [
                                       'Today' => 'today',
                                       'This month' => 'this_month',
                                       // ...
                                   ],
                               ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    {
        if ('today' === $form->getData()) {
            // use $metadata['property'] to make this query generic
            $queryBuilder->andWhere('entity.date = :today')
                         ->setParameter('today', (new \DateTime('today'))->format('Y-m-d'));
        }

    }
}
