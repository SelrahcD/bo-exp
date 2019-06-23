<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $repository_full_class_name ?>;
use <?= $entity_full_class_name ?>;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Persistence\ObjectManager;

class <?= $class_name ?> extends WebTestCase
{
    const GENERATED_ELEMENTS = 3;

    /**
     * @var <?= $repository_class_name ?>
     */
    private $<?= $repository_var ?>;

    /**
    * @var ObjectManager
    */
    private $manager;

    protected function setUp(): void
    {
        static::createClient();
        $container = self::$container;
        $this-><?= $repository_var ?> = $container->get(<?= $repository_class_name ?>::class);
        $this->manager = $container->get('doctrine.orm.default_entity_manager');
        $this-><?= $repository_var ?>->clear();
    }

    /**
     * @group DB
     */
    public function testListAll()
    {
        for($i = 0; $i < self::GENERATED_ELEMENTS; $i++)
        {
            $<?= $entity_variable ?> = (new <?= $entity_name ?>())
        <?php foreach ($entity_setters as $setter_name => $params): ?>
        -><?= $setter_name ?>(<?= implode(',', $params) ?>)
        <?php endforeach; ?>
    ;

            $this->manager->persist($<?= $entity_variable ?>);
        }

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '<?= $route_path ?>/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(self::GENERATED_ELEMENTS, $crawler->filter('tbody > tr')->count());
    }

    /**
    * @group DB
    */
    public function testShowOne() {

        $<?= $entity_variable ?> = (new <?= $entity_name ?>())
        <?php foreach ($entity_setters as $setter_name => $params): ?>
            -><?= $setter_name ?>(<?= implode(',', $params) ?>)
        <?php endforeach; ?>
        ;

        $this->manager->persist($<?= $entity_variable ?>);

        $this->manager->flush();

        $client = static::createClient();
        $client->request('GET', '<?= $route_path ?>/'. $<?= $entity_variable ?>->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testCreateOne() {

        $client = static::createClient();
        $crawler = $client->request('GET', '<?= $route_path ?>/new');

        $createForm = $crawler->selectButton('Save')->form();

        $createForm->setValues([
        <?php foreach ($entity_form_fields as $form_field => $form_field_value): ?>
            '<?= $entity_variable ?>[<?= $form_field ?>]' => null,
        <?php endforeach; ?>
        ]);

        $client->submit($createForm);

        $client->followRedirect();
        $crawler = $client->getCrawler();

        $this->assertEquals(1, $crawler->filter('tbody > tr')->count());
        $this->assertEquals(1, $this-><?= $repository_var ?>->count([]));
    }

    /**
    * @group DB
    */
    public function testUpdateOne() {

        $<?= $entity_variable ?> = (new <?= $entity_name ?>())
        <?php foreach ($entity_setters as $setter_name => $params): ?>
            -><?= $setter_name ?>(<?= implode(',', $params) ?>)
        <?php endforeach; ?>
        ;

        $this->manager->persist($<?= $entity_variable ?>);

        $this->manager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '<?= $route_path ?>/'. $<?= $entity_variable ?>->getId() . '/edit');

        $updateForm = $crawler->selectButton('Update')->form();

        $updateForm->setValues([
        <?php foreach ($entity_form_fields as $form_field => $form_field_value): ?>
            '<?= $entity_variable ?>[<?= $form_field ?>]' => null,
        <?php endforeach; ?>
        ]);

        $client->submit($updateForm);

        $client->followRedirect();

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    /**
    * @group DB
    */
    public function testDeleteOne() {

        $<?= $entity_variable ?> = (new <?= $entity_name ?>())
        <?php foreach ($entity_setters as $setter_name => $params): ?>
            -><?= $setter_name ?>(<?= implode(',', $params) ?>)
        <?php endforeach; ?>
        ;


        $this->manager->persist($<?= $entity_variable ?>);

        $this->manager->flush();

        $client = static::createClient();
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete'.$<?= $entity_variable ?>->getId());
        $client->request('DELETE', '<?= $route_path ?>/'. $<?= $entity_variable ?>->getId(), [
            '_token' => $csrfToken,
        ]);

        $this->assertNull($this-><?= $repository_var ?>->find($<?= $entity_variable ?>->getId()));
    }
}
