<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(10, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setContent($this->faker->sentence);

            /** @var Post $post */
            $post = $this->getRandomReference('posts');
            $comment->setPost($post);

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);


            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: PostFixtures::class, 1: UserFixtures::class}
     */
    public function getDependencies(): array
    {
        return [PostFixtures::class, UserFixtures::class];
    }
}
