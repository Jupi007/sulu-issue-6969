<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\CustomEntity;
use App\Repository\CustomEntityRepository;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type CustomEntityData array{
 *     id: int|null,
 *     title: string,
 * }
 */
class CustomEntityController extends AbstractController
{
    public function __construct(
        private readonly CustomEntityRepository $customEntityRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        private readonly TrashManagerInterface $trashManager,
    ) {
    }

    #[Route(path: '/admin/api/custom-entities', methods: ['GET'], name: 'app.get_custom_entity_list')]
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            CustomEntity::RESOURCE_KEY,
        );

        return $this->json($listRepresentation->toArray());
    }

    #[Route(path: '/admin/api/custom-entities/{id}', methods: ['GET'], name: 'app.get_custom_entity')]
    public function getAction(int $id): Response
    {
        $customEntity = $this->load($id);
        if (!$customEntity instanceof CustomEntity) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($customEntity));
    }

    #[Route(path: '/admin/api/custom-entities/{id}', methods: ['PUT'], name: 'app.put_custom_entity')]
    public function putAction(int $id, Request $request): Response
    {
        $customEntity = $this->load($id);
        if (!$customEntity instanceof CustomEntity) {
            throw new NotFoundHttpException();
        }

        /** @var CustomEntityData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $customEntity);
        $this->save($customEntity);

        return $this->json($this->getDataForEntity($customEntity));
    }

    #[Route(path: '/admin/api/custom-entities', methods: ['POST'], name: 'app.post_custom_entity')]
    public function postAction(Request $request): Response
    {
        $customEntity = $this->create();

        /** @var CustomEntityData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $customEntity);
        $this->save($customEntity);

        return $this->json($this->getDataForEntity($customEntity), 201);
    }

    #[Route(path: '/admin/api/custom-entities/{id}', methods: ['DELETE'], name: 'app.delete_custom_entity')]
    public function deleteAction(int $id): Response
    {
        if ($customEntity = $this->load($id)) {
            $this->trashManager->store(CustomEntity::RESOURCE_KEY, $customEntity);
            $this->remove($id);
        }

        return $this->json(null, 204);
    }

    /**
     * @return CustomEntityData
     */
    protected function getDataForEntity(CustomEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle() ?? '',
        ];
    }

    /**
     * @param CustomEntityData $data
     */
    protected function mapDataToEntity(array $data, CustomEntity $entity): void
    {
        $entity->setTitle($data['title']);
    }

    protected function load(int $id): ?CustomEntity
    {
        return $this->customEntityRepository->findById($id);
    }

    protected function create(): CustomEntity
    {
        return $this->customEntityRepository->create();
    }

    protected function save(CustomEntity $entity): void
    {
        $this->customEntityRepository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->customEntityRepository->remove($id);
    }
}
