<?php

declare(strict_types=1);

namespace NullDev\Skeleton\Command;

use Symfony\Component\Yaml\Yaml;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BaseBoundaryCommand extends BaseSkeletonGeneratorCommand
{
    protected function readClassConfig()
    {
        $path = $this->getClassConfigPath();

        // If there is no class config file, create default one.
        if (false === is_file($path)) {
            $this->writeClassConfig($this->getDefaultClassConfig());
        }

        return Yaml::parse(file_get_contents($path));
    }

    protected function writeClassConfig(array $config): void
    {
        file_put_contents(
            $this->getClassConfigPath(),
            Yaml::dump($config, 7, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }

    private function getDefaultClassConfig()
    {
        return [
            'boundaries' => [],
        ];
    }

    private function getClassConfigPath(): string
    {
        return getcwd().'/skeleton.yml';
    }
}
