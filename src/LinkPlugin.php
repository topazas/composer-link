<?php


namespace henzeb\ComposerLink;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use henzeb\ComposerLink\Filesystem\Filesystem;
use henzeb\ComposerLink\Manager\ComposerManager;
use henzeb\ComposerLink\Manager\ConfigManager;
use henzeb\ComposerLink\Manager\LinkManager;

class LinkPlugin implements PluginInterface, Capable, EventSubscriberInterface
{
    /**
     * @var LinkManager
     */
    private static $linkManager;

    public static function getSubscribedEvents(): array
    {
        if (static::isNoTemporaryClass()) {
            return [];
        }

        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => ['preAutoloadDump'],
        ];
    }

    /**
     * Composer 1 seems to load this plugin class three times. dunno why, but this ugly workaround should prevent it.
     *
     * @return bool
     */
    public static function isNoTemporaryClass(): bool
    {
        return __CLASS__ !== 'henzeb\\ComposerLink\\LinkPlugin';
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        if (static::isNoTemporaryClass()) {
            return;
        }
        $filesystem = new Filesystem();
        $composerManager = new ComposerManager($composer, $io);

        self::$linkManager = new LinkManager(
            $filesystem,
            new ConfigManager($filesystem, $composerManager->getExtra('linked', './linked.json')),
            $composerManager
        );
    }

    public function getLinkManager(): LinkManager
    {
        return self::$linkManager;
    }

    public function getCapabilities(): array
    {
        if (static::isNoTemporaryClass()) {
            return [];
        }

        return [
            CommandProvider::class => LinkCommandProvider::class,
        ];
    }

    public function preAutoloadDump(): void
    {
        self::$linkManager->linkAllFromConfig();
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    public function uninstall(Composer $composer, IOInterface $io)
    {

    }


}
