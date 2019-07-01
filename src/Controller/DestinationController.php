<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Destination;
use App\Repository\DestinationRepository;
use Doctrine\DBAL\Connection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

final class DestinationController extends EasyAdminController
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function findAll(
        $entityClass,
        $page = 1,
        $maxPerPage = 15,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {
        $query = $this->connection->createQueryBuilder()
            ->from('experience')
            ->select('id, title as name');

        $query->orderBy($sortField, $sortDirection);
        $query->setMaxResults($maxPerPage);
//        $query->setFirstResult($page);

        return new Pagerfanta(new ArrayAdapter(array_map(function($data) {
            return new Destination($data['id'], $data['name']);
        },
            $query->execute()->fetchAll()
        )));
//        return new Pagerfanta(new ArrayAdapter([new Destination(1,' Corse'), new Destination(2,'France')]));
     }

    protected function findBy(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $page = 1,
        $maxPerPage = 15,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null
    ) {

        $query = $this->connection->createQueryBuilder()
                                  ->from('experience')
                                  ->select('id, title as name');

        $query->orderBy($sortField, $sortDirection);
        $query->setMaxResults($maxPerPage);

        $query->where('title ILIKE ?')
            ->setParameter(0, '%' . $searchQuery . '%');

        return new Pagerfanta(new ArrayAdapter(array_map(function($data) {
            return new Destination($data['id'], $data['name']);
        },
            $query->execute()->fetchAll()
                                               )));

    }

    protected function createEntityFormBuilder($entity, $view)
    {
        return $this->createFormBuilder();
    }

}
