<?php

namespace Oro\Component\Layout\Extension\Theme\Model;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Creates instance of Theme model based on given name and definition.
 */
class ThemeFactory implements ThemeFactoryInterface
{
    /** @var PropertyAccessor */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function create($themeName, array $themeDefinition)
    {
        $theme = new Theme(
            $themeName,
            isset($themeDefinition['parent']) ? $themeDefinition['parent'] : null
        );

        $this->applyThemeProperties($theme, $themeDefinition);

        if (isset($themeDefinition['config'])) {
            $theme->setConfig($themeDefinition['config']);
        }

        $this->addPageTemplatesConfig($themeDefinition, $theme);

        return $theme;
    }

    /**
     * @param Theme $theme
     * @param array $themeDefinition
     */
    private function applyThemeProperties(Theme $theme, array $themeDefinition)
    {
        $properties = [
            'label',
            'screenshot',
            'icon',
            'logo',
            'image_placeholders',
            'directory',
            'groups',
            'description',
        ];

        foreach ($properties as $property) {
            if (isset($themeDefinition[$property])) {
                $this->propertyAccessor->setValue($theme, $property, $themeDefinition[$property]);
            }
        }
    }

    /**
     * @param array $themeDefinition
     * @param Theme $theme
     */
    private function addPageTemplatesConfig(array $themeDefinition, Theme $theme)
    {
        if (isset($themeDefinition['config']['page_templates']['titles'])) {
            foreach ($themeDefinition['config']['page_templates']['titles'] as $routeKey => $title) {
                $theme->addPageTemplateTitle($routeKey, $title);
            }
        }

        if (isset($themeDefinition['config']['page_templates']['templates'])) {
            foreach ($themeDefinition['config']['page_templates']['templates'] as $pageTemplateConfig) {
                $pageTemplate = new PageTemplate(
                    $pageTemplateConfig['label'],
                    $pageTemplateConfig['key'],
                    $pageTemplateConfig['route_name']
                );
                if (isset($pageTemplateConfig['description'])) {
                    $pageTemplate->setDescription($pageTemplateConfig['description']);
                }
                if (isset($pageTemplateConfig['screenshot'])) {
                    $pageTemplate->setScreenshot($pageTemplateConfig['screenshot']);
                }
                if (isset($pageTemplateConfig['enabled'])) {
                    $pageTemplate->setEnabled($pageTemplateConfig['enabled']);
                }

                $theme->addPageTemplate($pageTemplate);
            }
        }
    }
}
