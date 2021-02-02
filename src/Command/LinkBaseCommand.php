<?php

namespace henzeb\ComposerLink\Command;

use Composer\Command\BaseCommand;
use henzeb\ComposerLink\Command\Interpreter\Interpreter;
use henzeb\ComposerLink\Manager\LinkManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LinkBaseCommand extends BaseCommand
{

    /**
     * @var LinkManager
     */
    private $linkManager;

    public function __construct(LinkManager $linkManager)
    {
        parent::__construct();

        $this->linkManager = $linkManager;
    }

    abstract public function getCommandStrategies(): array;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $strategy = $this->getInterpreter()->interpret($input);

        if ($strategy) {
            return $strategy->execute($this->linkManager, $input, $output);
        }
    }

    private function getInterpreter(): Interpreter
    {
        return new Interpreter(
            ...$this->getCommandStrategies()
        );
    }
}
