<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function renderMenu(string $activeRoute): Response
    {
        $menuItems = [
            $this->generateMenuItem(
                $this->translator->trans('Dashboard', [], 'sidebar'),
                $this->translator->trans('Go to dashboard', [], 'sidebar'),
                'default',
                'tachometer-alt',
                $activeRoute
            ),
            $this->generateMenuItem(
                $this->translator->trans('Case list', [], 'sidebar'),
                $this->translator->trans('Go to list of cases', [], 'sidebar'),
                'case_index',
                'list',
                $activeRoute
            ),
            $this->generateMenuItem(
                $this->translator->trans('Settings', [], 'sidebar'),
                $this->translator->trans('Go to settings', [], 'sidebar'),
                'admin',
                'cog',
                $activeRoute
            ),
        ];

        return $this->render('sidebar/_menu.html.twig', [
            'menu_items' => $menuItems,
        ]);
    }

    private function generateMenuItem(string $name, string $tooltip, string $route, string $icon, $activeRoute): array
    {
        return [
            'name' => $this->translator->trans($name, [], 'sidebar'),
            'tooltip' => $this->translator->trans($tooltip, [], 'sidebar'),
            'link' => $this->generateUrl($route),
            'icon' => $icon,
            'active' => $activeRoute === $route,
        ];
    }

    public function renderSubmenu(UuidV4 $caseId, string $activeRoute): Response
    {
        $submenuItems = [
            $this->generateSubmenuItem($this->translator->trans('Summary', [], 'sidebar'), ['case_summary'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Basic Information', [], 'sidebar'), ['case_show', 'case_edit', 'party_add', 'party_add_from_index', 'party_edit'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Status Info', [], 'sidebar'), ['case_status'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Hearing', [], 'sidebar'), ['case_hearing'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Communication', [], 'sidebar'), ['case_communication'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Documents', [], 'sidebar'), ['document_index', 'document_create', 'document_copy'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Decision', [], 'sidebar'), ['case_decision'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Notes', [], 'sidebar'), ['note_index', 'note_edit'], $caseId, $activeRoute),
            $this->generateSubmenuItem($this->translator->trans('Log', [], 'sidebar'), ['case_log'], $caseId, $activeRoute),
        ];

        return $this->render('sidebar/_submenu.html.twig', [
            'submenu_items' => $submenuItems,
        ]);
    }

    /**
     * Notice that the generated link uses the first route in array of routes,
     * when it generates the url.
     */
    private function generateSubmenuItem(string $name, array $routes, string $caseId, $activeRoute): array
    {
        return [
            'name' => $this->translator->trans($name, [], 'sidebar'),
            'link' => $this->generateUrl($routes[0], ['id' => $caseId]),
            'active' => in_array($activeRoute, $routes),
        ];
    }
}
