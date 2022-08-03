<?php

namespace jtl\CustomProductTabs;

use DI\Container;
use Jtl\Connector\Core\Definition\Action;
use Jtl\Connector\Core\Definition\Controller;
use Jtl\Connector\Core\Definition\Event;
use Jtl\Connector\Core\Plugin\PluginInterface;
use JtlWooCommerceConnector\Utilities\SupportedPlugins;
use JtlWooCommerceConnector\Utilities\Util;
use Noodlehaus\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 */
class Bootstrap implements PluginInterface
{
    public const
        CUSTOM_PRODUCT_TABS_FOR_WOO_COMMERCE_PLUGIN = 'Custom Product Tabs for WooCommerce';

    /**
     * @param ConfigInterface $config
     * @param Container $container
     * @param EventDispatcher $dispatcher
     * @return void
     * @throws \Jtl\Connector\Core\Exception\DefinitionException
     * @throws \ReflectionException
     */
    public function registerListener(ConfigInterface $config, Container $container, EventDispatcher $dispatcher)
    {
        if (SupportedPlugins::isActive(self::CUSTOM_PRODUCT_TABS_FOR_WOO_COMMERCE_PLUGIN)) {
            $dispatcher->addListener(Event::createEventName(Controller::PRODUCT, Action::PUSH, Event::AFTER), [
                new ProductListener($container->get(Util::class)),
                'onProductAfterPush'
            ]);
        }
    }
}
