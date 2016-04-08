<?php

namespace Alpixel\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DateTimeSingleType.
 */
class DateTimeSingleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format'      => 'd-m-Y H:i',
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
        ]);
    }

    public function getParent()
    {
        return DateTimeType::class;
    }
}
