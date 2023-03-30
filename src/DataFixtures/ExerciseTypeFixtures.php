<?php

namespace App\DataFixtures;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeParam;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ExerciseTypeFixtures extends Fixture
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private FileUploader $fileUploader
    ){}

    const LENGTH = 10;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        $videoViews = ['midLeft', 'midRight', 'front', 'side'];

        $videosDirectory = __DIR__ . '/videos';
        $availableVideos = glob($videosDirectory . '/*.{mp4}', GLOB_BRACE);

        // Clear target directory
        $fs = new Filesystem();
        $fs->remove( glob($this->parameterBag->get('uploads_directory') . '/exercise/*') );

        for ($i = 0; $i < self::LENGTH; $i++) {

            $exerciseType = new ExerciseType();
            $exerciseType->setName($faker->name);
            $exerciseType->setDescription($faker->text);
            $exerciseType->setDefaultVideoView($videoViews[rand(0,3)]);
            $exerciseType->setCode($faker->word);

            $randomVideo = $faker->randomElement($availableVideos);

            // Copy video before upload (move)
            $targetPath = str_replace($videosDirectory, sys_get_temp_dir(), $randomVideo);
            $fs->copy($randomVideo, $targetPath, true);

            // Upload Video
            $previewVideoPath = $this->fileUploader->upload('videos', new File($targetPath));
            $exerciseType->setInstructionVideo($previewVideoPath);

            // Params
            for ($p = 0; $p < rand(3, 5); $p++){

                $exerciseTypeParam = new ExerciseTypeParam();
                $exerciseTypeParam->setName($faker->name);
                $exerciseType->addExerciseTypeParam($exerciseTypeParam);

            }

            $manager->persist($exerciseType);

        }

        $manager->flush();
    }
}
