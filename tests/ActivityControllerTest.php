<?php

namespace App\Tests;

use App\Repository\ActivityRepository;
use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Persistence\ObjectManager;

class ActivityControllerTest extends WebTestCase
{
    const GENERATED_ELEMENTS = 3;

    /**
     * @var ActivityRepository     */
    private $activityRepository;

    /**
    * @var ObjectManager
    */
    private $manager;

    protected function setUp(): void
    {
        static::createClient();
        $container = self::$container;
        $this->activityRepository = $container->get(ActivityRepository::class);
        $this->manager = $container->get('doctrine.orm.default_entity_manager');
        $this->activityRepository->clear();
    }

    /**
     * @group DB
     */
    public function testListAll()
    {
        for($i = 0; $i < self::GENERATED_ELEMENTS; $i++)
        {
            $activity = (new Activity())
                ->setTitle('A title')
                ->setDescription('A description')
                ->setLocation(17)
            ;

            $this->manager->persist($activity);
        }

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/activity/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(self::GENERATED_ELEMENTS, $crawler->filter('tbody > tr')->count());
    }

    /**
    * @group DB
    */
    public function testShowOne() {

        $activity = (new Activity())
                    ->setTitle('A title')
                    ->setDescription('A description')
                    ->setLocation(17)
                ;

        $this->manager->persist($activity);

        $this->manager->flush();

        $client = static::createClient();
        $client->request('GET', '/activity/'. $activity->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testCreateOne() {

        $client = static::createClient();
        $crawler = $client->request('GET', '/activity/new');

        $createForm = $crawler->selectButton('Save')->form();

        $createForm->setValues([
                    'activity[title]' => 'New title',
                    'activity[Description]' => 'New description',
                    'activity[Location]' => 19,
                ]);

        $client->submit($createForm);

        $client->followRedirect();
        $crawler = $client->getCrawler();

        $this->assertEquals(1, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $this->activityRepository->count([]));
    }

    /**
    * @group DB
    */
    public function testUpdateOne() {

        $activity = (new Activity())
                    ->setTitle('A title')
                    ->setDescription('A description')
                    ->setLocation(17)
                ;

        $this->manager->persist($activity);

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/activity/'. $activity->getId() . '/edit');

        $updateForm = $crawler->selectButton('Update')->form();

        $updateForm->setValues([
                    'activity[title]' => 'New title',
                    'activity[Description]' => 'New description',
                    'activity[Location]' => 10,
                ]);

        $client->submit($updateForm);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testDeleteOne() {

        $activity = (new Activity())
                    ->setTitle('A title')
                    ->setDescription('A description')
                    ->setLocation(17)
                ;


        $this->manager->persist($activity);

        $this->manager->flush();

        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete'.$activity->getId());
        $client->request('DELETE', '/activity/'. $activity->getId(), [
            '_token' => $csrfToken,
        ]);

        $this->assertNull($this->activityRepository->find($activity->getId()));
    }
}
