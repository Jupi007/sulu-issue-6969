<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Trash;

use App\Admin\CustomEntityAdmin;
use App\Entity\CustomEntity;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;

final class CustomEntityTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    private TrashItemRepositoryInterface $trashItemRepository;
    private EntityManagerInterface $entityManager;
    private DoctrineRestoreHelperInterface $doctrineRestoreHelper;

    public function __construct(
        TrashItemRepositoryInterface $trashItemRepository,
        EntityManagerInterface $entityManager,
        DoctrineRestoreHelperInterface $doctrineRestoreHelper,
    ) {
        $this->trashItemRepository = $trashItemRepository;
        $this->doctrineRestoreHelper = $doctrineRestoreHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * @param CustomEntity $album
     */
    public function store(object $album, array $options = []): TrashItemInterface
    {
        $data = [
            'title' => $album->getTitle(),
        ];

        return $this->trashItemRepository->create(
            CustomEntity::RESOURCE_KEY,
            (string) $album->getId(),
            $album->getTitle(),
            $data,
            null,
            $options,
            null,
            null,
            null,
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /**
         * @var array{
         *     title: string,
         * } $data
         */
        $data = $trashItem->getRestoreData();

        $album = new CustomEntity();
        $album->setTitle($data['title']);

        $this->doctrineRestoreHelper->persistAndFlushWithId($album, (int) $trashItem->getResourceId());

        return $album;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, CustomEntityAdmin::EDIT_FORM_VIEW, ['id' => 'id']);
    }

    public static function getResourceKey(): string
    {
        return CustomEntity::RESOURCE_KEY;
    }
}
