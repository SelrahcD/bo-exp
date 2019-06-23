<?php

namespace App\Tests;

use App\Repository\PonyRepository;
use App\Entity\Pony;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Persistence\ObjectManager;

class PonyControllerTest extends WebTestCase
{
    const GENERATED_ELEMENTS = 3;

    /**
     * @var PonyRepository     */
    private $ponyRepository;

    /**
    * @var ObjectManager
    */
    private $manager;

    protected function setUp(): void
    {
        static::createClient();
        $container = self::$container;
        $this->ponyRepository = $container->get(PonyRepository::class);
        $this->manager = $container->get('doctrine.orm.default_entity_manager');
        $this->ponyRepository->clear();
    }

    /**
     * @group DB
     */
    public function testListAll()
    {
        for($i = 0; $i < self::GENERATED_ELEMENTS; $i++)
        {
            $pony = (new Pony())
                ->setName('Eole')
                ->setBirthdate(new \DateTimeImmutable('2015-06-06'))
            ;

            $this->manager->persist($pony);
        }

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/pony/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(self::GENERATED_ELEMENTS, $crawler->filter('tbody > tr')->count());
    }

    /**
    * @group DB
    */
    public function testShowOne() {

        $pony = (new Pony())
                    ->setName('Eole')
                    ->setBirthdate(new \DateTimeImmutable('2015-06-06'))
                ;

        $this->manager->persist($pony);

        $this->manager->flush();

        $client = static::createClient();
        $client->request('GET', '/pony/'. $pony->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testCreateOne() {

        $client = static::createClient();
        $crawler = $client->request('GET', '/pony/new');

        $createForm = $crawler->selectButton('Save')->form();

        $createForm->setValues([
                    'pony[name]' => 'Griotte',
                    'pony[birthdate][date][year]' => 2016,
                    'pony[birthdate][date][month]' => 8,
                    'pony[birthdate][date][day]' => 6,
                ]);

        $client->submit($createForm);

        $client->followRedirect();
        $crawler = $client->getCrawler();

        $this->assertEquals(1, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $this->ponyRepository->count([]));
    }

    /**
    * @group DB
    */
    public function testUpdateOne() {

        $pony = (new Pony())
                    ->setName('Eole')
                    ->setBirthdate(new \DateTimeImmutable('2015-06-06'))
                ;

        $this->manager->persist($pony);

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/pony/'. $pony->getId() . '/edit');

        $updateForm = $crawler->selectButton('Update')->form();

        $updateForm->setValues([
                    'pony[name]' => 'Griotte',
                    'pony[birthdate][date][year]' => 2016,
                    'pony[birthdate][date][month]' => 8,
                    'pony[birthdate][date][day]' => 6,
                ]);

        $client->submit($updateForm);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testDeleteOne() {

        $pony = (new Pony())
                    ->setName('Eole')
                    ->setBirthdate(new \DateTimeImmutable('2015-06-06'))
                ;


        $this->manager->persist($pony);

        $this->manager->flush();

        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete'.$pony->getId());
        $client->request('DELETE', '/pony/'. $pony->getId(), [
            '_token' => $csrfToken,
        ]);

        $this->assertNull($this->ponyRepository->find($pony->getId()));
    }
}
