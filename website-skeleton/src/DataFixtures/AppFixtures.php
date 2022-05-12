<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\User;
use App\Entity\Tricks;
use App\Entity\Video;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
       for ($i=0; $i <20 ; $i++) { 
        $user = new User();
        $user->setRoles(['ROLE_USER'])
             ->setName($faker->firstName())
             ->setEmail($faker->email())
             ->setAvatar($faker->imageUrl('cats'))
             ->setIsVerified(true);

             $password = $this->hasher->hashPassword($user,'password');
             $user->setPassword($password);




        $manager->persist($user);
       }
      
       for ($i=0; $i <4 ; $i++) { 
           $category= new Category();
           $category->setName($faker->word(3,true));
           $manager->persist($category);

       }

       for ($i=0; $i <20 ; $i++) { 
        $tricks = new Tricks();
        $tricks->setTitle($faker->word(3,true))
               ->setContent($faker->text(5))
               ->setFeatImg($faker->imageUrl('cats'))
               ->setCreatedAt($faker->dateTime())
               ->setCategory($category)
               ->setUser($user);
        




        $manager->persist($tricks);
       }
       for ($i=0; $i < 15 ; $i++) { 
        $image = new Image();
        $image->setName($faker->imageUrl('cat'));
        $image->setTricks($tricks);

        $manager->persist($image);

    }
    
    for ($i=0; $i <10 ; $i++) {
        $video = new Video();
        $video->setUrl($faker->word(3,true));
        $video->setTricks($tricks);

    
     $manager->persist($video);

 }
 for ($i=0; $i <100 ; $i++) {
    $comment = new Comments();
    $comment->setCreatedAt($faker->dateTime())
           ->setComment($faker->text(5))
           ->setTricks($tricks)
           ->setUser($user);


 $manager->persist($comment);

}
       $manager->flush();

       
    }
}
