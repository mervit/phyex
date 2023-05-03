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

        $videoViews = ['midLeft', 'midRight', 'front', 'side'];

        $this->videosDirectory = __DIR__ . '/videos';
        $availableVideos = glob($this->videosDirectory . '/*.{mp4}', GLOB_BRACE);

        // Clear target directory
        $this->fs = new Filesystem();
        $this->fs->remove( glob($this->parameterBag->get('uploads_directory') . '/videos/*') );

        // Inicialize fakers
        $faker = Factory::create();

        $exercises = [];
        foreach($availableVideos as $filePath){

            $videoName = substr($filePath, strrpos($filePath, '/') + 1, -10);
            $exercises[] = $videoName;
        }

        $exercises = array_unique($exercises);
        $i = 1;
        foreach($exercises as $exerciseName){

            $exerciseNameParts = explode('_', $exerciseName);

            // Figurant
            $figurantReferenceName = 'Figurant_' . $exerciseNameParts[1];
            if($this->hasReference($figurantReferenceName)) {
                $figurant = $this->getReference($figurantReferenceName);
            } else {
                $figurant = new Figurant();
                $figurant->setNickname($faker->firstName);
                $figurant->setGender(rand(0, 1) === 0 ? 'Male': 'Female');
                $figurant->setHeight(rand(154, 190));
                $figurant->setWeight(rand(52, 102));
                $figurant->setActiveHoursPerWeek(rand(2,7));
                $figurant->setAge(rand(18, 75));
                $figurant->setOccupation($faker->name);
                $figurant->setSittingTimePerDay(rand(1, 16));
                $figurant->setStretchingFrequency(rand(1, 16));
                $figurant->setPublicVideoConfirmation(rand(0,1) === 0);
                $figurant->setSportHoursPerWeek(rand(2,7));
                $manager->persist($figurant);
                $this->setReference($figurantReferenceName, $figurant);
            }

            // ExerciseType
            $exerciseTypeName = str_replace($exerciseNameParts[0] . '_' . $exerciseNameParts[1] . '_' . $exerciseNameParts[2] . '_' . $exerciseNameParts[3] . '_', '', $exerciseName);
            $exerciseTypeReferenceName = 'ExerciseType_' . $exerciseTypeName;
            if(!$this->hasReference($exerciseTypeReferenceName)){

                $exerciseType = new ExerciseType();
                $exerciseType->setName($exerciseTypeName);
                $exerciseType->setDescription($faker->text);
                $exerciseType->setDefaultVideoView($videoViews[rand(0,3)]);
                $exerciseType->setCode($exerciseTypeName);

                $randomVideo = $faker->randomElement($availableVideos);

                // Copy video before upload (move)
                $targetPath = str_replace($this->videosDirectory, sys_get_temp_dir(), $randomVideo);
                $this->fs->copy($randomVideo, $targetPath, true);

                // Upload Video
                $previewVideoPath = $this->fileUploader->upload('videos', new File($targetPath));
                $exerciseType->setInstructionVideo($previewVideoPath);


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
            $this->setReference('Exercise_' . $i, $exercise);
            $i++;

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
