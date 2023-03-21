<?php

namespace App\DataFixtures;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeParam;
use App\Entity\Figurant;
use App\Entity\Image;
use App\Entity\Exercise;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ExerciseFixtures extends Fixture
{

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private FileUploader $fileUploader
    ){}

    const LENGTH = 10;

    private string $videosDirectory;
    private Filesystem $fs;

    public function load(ObjectManager $manager): void
    {

        $this->videosDirectory = __DIR__ . '/videos';
        $availableVideos = glob($this->videosDirectory . '/*.{mp4}', GLOB_BRACE);

        // Clear target directory
        $this->fs = new Filesystem();
        $this->fs->remove( glob($this->parameterBag->get('uploads_directory') . '/exercise/*') );

        // Inicialize fakers
        $faker = Factory::create();

        $exercises = [];
        foreach($availableVideos as $filePath){

            $videoName = substr($filePath, strrpos($filePath, '/') + 1, -10);
            $exercises[] = $videoName;
        }

        $exercises = array_unique($exercises);

        foreach($exercises as $exerciseName){

            $exerciseNameParts = explode('_', $exerciseName);

            // Figurant
            $figurantReferenceName = 'Figurant_' . $exerciseNameParts[1];
            if($this->hasReference($figurantReferenceName)) {
                $figurant = $this->getReference($figurantReferenceName);
            } else {
                $figurant = new Figurant();
                $figurant->setFirstname($faker->firstName);
                $figurant->setSurname($faker->lastName);
                $manager->persist($figurant);
                $this->setReference($figurantReferenceName, $figurant);
            }

            // ExerciseType
            $exerciseTypeName = str_replace($exerciseNameParts[0] . '_' . $exerciseNameParts[1] . '_' . $exerciseNameParts[2] . '_' . $exerciseNameParts[3] . '_', '', $exerciseName);
            $exerciseTypeReferenceName = 'ExerciseType_' . $exerciseTypeName;
            if(!$this->hasReference($exerciseTypeReferenceName)){

                $exerciseType = new ExerciseType();
                $exerciseType->setName($exerciseTypeName);

                // Exercise type params
                for ($p = 0; $p < rand(3, 5); $p++){

                    $exerciseTypeParam = new ExerciseTypeParam();
                    $exerciseTypeParam->setName($faker->name);
                    $exerciseType->addExerciseTypeParam($exerciseTypeParam);

                }
                $manager->persist($exerciseType);
                $this->setReference($exerciseTypeReferenceName, $exerciseType);

            } else {
                $exerciseType = $this->getReference($exerciseTypeReferenceName);
            }

            $exercise = new Exercise();
            $exercise->setVideoMidLeft($this->addVideoFile($exerciseName, 1));
            $exercise->setVideoMidRight($this->addVideoFile($exerciseName, 2));
            $exercise->setVideoFront($this->addVideoFile($exerciseName, 3));
            $exercise->setVideoSide($this->addVideoFile($exerciseName, 4));
            $exercise->setFigurant($figurant);
            $exercise->setExerciseType($exerciseType);
            $exercise->setDatetime(new \Datetime(date(DATE_ATOM, $exerciseNameParts[2])));
            $manager->persist($exercise);

        }

        $manager->flush();
    }

    function addVideoFile($videoName, $cameraNumber){

        // Copy image before upload (move)
        $filePath = __DIR__ . '/videos/' . $videoName . '_cam_' . $cameraNumber . '.mp4';
        $targetPath = str_replace($this->videosDirectory, sys_get_temp_dir(), $filePath);
        $this->fs->copy($filePath, $targetPath, true);

        // Upload Image
        return $this->fileUploader->upload('videos', new File($targetPath));

    }
}
