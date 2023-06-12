<?php
/**
 * Post type.
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PostType.
 */
class PostType extends AbstractType
{
    /**
     * Tags data transformer.
     *
     * @var TagsDataTransformer
     */
    private TagsDataTransformer $tagsDataTransformer;

    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(TagsDataTransformer $tagsDataTransformer)
    {
        $this->tagsDataTransformer = $tagsDataTransformer;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'Title',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]);
        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => function ($category): string {
                    return $category->getTitle();
                },
                'label' => 'Category',
                'placeholder' => 'none',
                'required' => true,
            ]
        );
        $builder->add(
            'author',
            EntityType::class,
            [
                'class' => User::class,
                'choice_label' => function ($user): string {
                    return $user->getUsername();
                },
                'label' => 'Author',
                'placeholder' => 'None',
                'required' => true,
            ]
        );
        $builder->add(
            'content',
            TextType::class,
            [
                'label' => 'Content',
                'required' => true,
                'attr' => ['max_length' => 600],
            ]);
        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'Tags',
                'required' => false,
                'attr' => ['max_length' => 128],
            ]
        );

        $builder->get('tags')->addModelTransformer(
            $this->tagsDataTransformer
        );

        $builder->add(
            'comment',
            TextType::class,
            [
                'label' => 'Comment',
                'required' => false,
                'attr' => ['max_length' => 255],
            ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Post::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'post';
    }
}
