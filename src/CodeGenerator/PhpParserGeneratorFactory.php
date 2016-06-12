<?php

declare (strict_types = 1);
namespace NullDev\Skeleton\CodeGenerator;

use NullDev\Skeleton\CodeGenerator\PhpParser\ClassGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\MethodFactory;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\ConstructorGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\DeserializeGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\GetterGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\SerializeGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\ToStringGenerator;
use NullDev\Skeleton\CodeGenerator\PhpParser\Methods\UuidCreateGenerator;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;

class PhpParserGeneratorFactory
{
    public static function create() : PhpParserGenerator
    {
        $generator = new PhpParserGenerator(
            new BuilderFactory(),
            new ClassGenerator(
                new BuilderFactory()
            ),
            new MethodFactory(
                new ConstructorGenerator(new BuilderFactory()),
                new DeserializeGenerator(new BuilderFactory()),
                new GetterGenerator(new BuilderFactory()),
                new SerializeGenerator(new BuilderFactory()),
                new ToStringGenerator(new BuilderFactory()),
                new UuidCreateGenerator(new BuilderFactory())
            ),
            new PrettyPrinter\Standard()
        );

        return $generator;
    }
}