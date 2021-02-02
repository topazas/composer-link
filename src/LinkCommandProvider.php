<?php


namespace henzeb\ComposerLink;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider;
use henzeb\ComposerLink\Command\LinkCommand;
use henzeb\ComposerLink\Command\UnlinkCommand;
use henzeb\ComposerLink\Manager\LinkManager;

class LinkCommandProvider implements CommandProvider
{
    /**
     * @var LinkManager
     */
    private $linkManager;

    public function __construct($linkManager)
    {
        $this->linkManager = $linkManager['plugin']->getLinkmanager();
    }

    /**
     * @return array|BaseCommand[]
     */
    public function getCommands(): array
    {
        return [
            new LinkCommand($this->linkManager),
            new UnlinkCommand($this->linkManager),
        ];
    }
}
