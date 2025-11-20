<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Keyboard;
use App\Entity\Showcase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class KeyboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('brand')
            ->add('switchType')
            ->add('keycapSet')
            ->add('description')
            ->add('inventory', EntityType::class, [
                'class' => Inventory::class,
                'choice_label' => 'id',
            ])
            ->add('showcases', EntityType::class, [
                'class' => Showcase::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo du clavier (Image file)',
                // mapped => false signifie que ce champ du formulaire n'est pas directement
                // lié à la propriété "image" de l'entité (qui est une string),
                // car ici on manipule un fichier binaire.
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPG, PNG, WEBP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Keyboard::class,
        ]);
    }
}
