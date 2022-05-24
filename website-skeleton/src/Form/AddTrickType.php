<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddTrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content',TextareaType::class)
            ->add('videos', CollectionType::class,[
                'entry_type'=>VideoType::class,
                'by_reference'=>false,
                'allow_add'=>true,
                'allow_delete'=>true,
                'error_bubbling' => false

            ])
            ->add('Category',EntityType::class, [
                'class'   => Category::class,
                'choice_label'=>'name',
                'label'=>'Category',
                'query_builder'=>function(CategoryRepository $categoryRepository){

                    return $categoryRepository->createQueryBuilder('c')->OrderBy('c.name','ASC');
                },

            ])
            ->add('featimg',FileType::class,[
                'label'=> 'image à la une',
                'multiple'=>false,
                'mapped'=>false,
                'required'=>false,
                
            ])
            ->add('name',FileType::class,[
                'label'=> 'images',
                'multiple'=>true,
                'mapped'=>false,
                'required'=>true
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
