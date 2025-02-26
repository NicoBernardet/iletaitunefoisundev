<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Admin\Controller\CourseCrudController;
use App\Admin\Entity\Administrator;
use App\Content\Doctrine\Repository\CourseRepository;
use App\Content\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CourseTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldShowListOfCourses(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var Administrator $admin */
        $admin = $entityManager->getRepository(Administrator::class)->findOneBy(['email' => 'admin+1@email.com']);

        $client->loginUser($admin, 'admin');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(CourseCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function shouldCreateCourse(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var Administrator $admin */
        $admin = $entityManager->getRepository(Administrator::class)->findOneBy(['email' => 'admin+1@email.com']);

        $client->loginUser($admin, 'admin');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(CourseCrudController::class)
                ->setAction(Action::NEW)
                ->generateUrl()
        );

        self::assertResponseIsSuccessful();

        $client->submitForm('Créer', [
            'Course[title]' => 'Course 126',
            'Course[description]' => 'Description',
            'Course[content]' => 'Content',
            'Course[youtubeUrl]' => 'https://www.youtube.com/watch?v=-S94RNjjb4I',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var CourseRepository<Course> $courseRepository */
        $courseRepository = $client->getContainer()->get(CourseRepository::class);

        $course = $courseRepository->findOneBy(['title' => 'Course 126']);

        self::assertNotNull($course);
        self::assertSame('Course 126', $course->getTitle());
        self::assertSame('Description', $course->getDescription());
        self::assertSame('Content', $course->getContent());
        self::assertSame('https://www.youtube.com/watch?v=-S94RNjjb4I', $course->getYoutubeUrl());
    }

    /**
     * @test
     */
    public function shouldShowDetailOfCourse(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var Administrator $admin */
        $admin = $entityManager->getRepository(Administrator::class)->findOneBy(['email' => 'admin+1@email.com']);

        $client->loginUser($admin, 'admin');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        /** @var Course $course */
        $course = $entityManager->getRepository(Course::class)->findOneBy(['title' => 'Course 1']);

        $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(CourseCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($course->getId())
                ->generateUrl()
        );

        self::assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function shouldUpdateCourse(): void
    {
        $client = self::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var Administrator $admin */
        $admin = $entityManager->getRepository(Administrator::class)->findOneBy(['email' => 'admin+1@email.com']);

        /** @var Course $course */
        $course = $entityManager->getRepository(Course::class)->findOneBy(['title' => 'Course 1']);

        $client->loginUser($admin, 'admin');

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $client->getContainer()->get(AdminUrlGenerator::class);

        $client->request(
            'GET',
            $adminUrlGenerator
                ->setController(CourseCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId((string) $course->getId())
                ->generateUrl()
        );

        self::assertResponseIsSuccessful();

        $client->submitForm('Sauvegarder les modifications', [
            'Course[title]' => 'Course 0',
            'Course[description]' => 'Description',
            'Course[content]' => 'Content',
            'Course[youtubeUrl]' => 'https://www.youtube.com/watch?v=-S94RNjjb4I',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var CourseRepository<Course> $courseRepository */
        $courseRepository = $client->getContainer()->get(CourseRepository::class);

        $course = $courseRepository->findOneBy(['title' => 'Course 0']);

        self::assertNotNull($course);
        self::assertSame('Course 0', $course->getTitle());
        self::assertSame('Description', $course->getDescription());
        self::assertSame('Content', $course->getContent());
        self::assertSame('https://www.youtube.com/watch?v=-S94RNjjb4I', $course->getYoutubeUrl());
    }
}
