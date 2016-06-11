<?php

declare (strict_types = 1);
namespace spec\NullDev\Skeleton\Generator;

use NullDev\Skeleton\Definition\PHP\ClassSource;
use NullDev\Skeleton\Generator\PhpParser\ClassGenerator;
use NullDev\Skeleton\Generator\PhpParser\MethodFactory;
use NullDev\Skeleton\Source\ImprovedClassSource;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinterAbstract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpParserGeneratorSpec extends ObjectBehavior
{
    public function let(
        BuilderFactory $builderFactory,
        ClassGenerator $classGenerator,
        MethodFactory $methodFactory,
        PrettyPrinterAbstract $printer,
        ImprovedClassSource $classSource
    ) {
        $this->beConstructedWith($builderFactory, $classGenerator, $methodFactory, $printer, $classSource);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('NullDev\Skeleton\Generator\PhpParserGenerator');
    }
}
