<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\CustomEntity;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

class CustomEntityAdmin extends Admin
{
    final public const LIST_KEY = 'custom_entities';
    final public const LIST_VIEW = 'app.custom_entities_list';
    final public const ADD_FORM_VIEW = 'app.custom_entity_add_form';
    final public const EDIT_FORM_VIEW = 'app.custom_entity_edit_form';

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $item = new NavigationItem('Custom entities');
        $item->setPosition(10);
        $item->setView(static::LIST_VIEW);

        $navigationItemCollection->add($item);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listToolbarActions = [
            new ToolbarAction('sulu_admin.add'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::LIST_VIEW, '/custom-entities')
            ->setResourceKey(CustomEntity::RESOURCE_KEY)
            ->setListKey(self::LIST_KEY)
            ->setTitle('Custom entities')
            ->addListAdapters(['table'])
            ->setAddView(static::ADD_FORM_VIEW)
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(self::ADD_FORM_VIEW, '/custom-entities/add')
            ->setResourceKey(CustomEntity::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(self::ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey(CustomEntity::RESOURCE_KEY)
            ->setFormKey('custom_entity_details')
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/custom-entities/:id')
            ->setResourceKey(CustomEntity::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW)
            ->setTitleProperty('title');
        $viewCollection->add($editFormView);

        $formToolbarActions = [
            new ToolbarAction('sulu_admin.save'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(CustomEntity::RESOURCE_KEY)
            ->setFormKey('custom_entity_details')
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}
