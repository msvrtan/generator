<?php

declare(strict_types=1);

namespace NullDev\Skeleton\Command;

use NullDev\Skeleton\CodeGenerator\PhpParserGeneratorFactory;
use NullDev\Skeleton\Definition\PHP\Parameter;
use NullDev\Skeleton\Definition\PHP\Types\Type;
use NullDev\Skeleton\Definition\PHP\Types\TypeFactory;
use NullDev\Skeleton\File\FileFactory;
use NullDev\Skeleton\File\FileGenerator;
use NullDev\Skeleton\File\FileResource;
use NullDev\Skeleton\Path\Readers\SourceCodePathReader;
use NullDev\Skeleton\Paths;
use NullDev\Skeleton\Source\ClassSourceFactory;
use NullDev\Skeleton\Source\ImprovedClassSource;
use NullDev\Skeleton\SpecGenerator\SpecGenerator;
use PhpSpec\Exception\Example\PendingException;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BaseSkeletonGeneratorCommand extends Command
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;

    private $paths;
    private $existingNamespaces;
    private $existingClasses;

    protected function getConstuctorParameters(): array
    {
        $fields = [];

        while (true) {
            $parameterClassName = $this->askForParameterClassName();

            if (true === empty($parameterClassName)) {
                break;
            }
            $parameterClassType = $this->createClassFromParameterClassName($parameterClassName);
            $parameterName      = $this->askForParameterName(lcfirst($parameterClassType->getName()));

            $fields[] = new Parameter($parameterName, $parameterClassType);
        }

        return $fields;
    }

    protected function handleClassNameInput()
    {
        if (false === empty($this->input->getOption('className'))) {
            return str_replace('/', '\\', $this->input->getOption('className'));
        }
        $this->getQuestionHelper()->writeSection($this->output, $this->getSectionMessage());

        $this->output->writeln($this->getIntroductionMessage());

        $className= null;
        while (true) {
            $className = $this->askForClassName();

            if (true === empty($className)) {
                $this->output->writeln('No class name, please enter it');
            } else {
                break;
            }
        }

        return str_replace('/', '\\', $className);
    }

    protected function handleGeneratingFile(FileResource $fileResource)
    {
        if ($this->fileNotExistsOrShouldBeOwerwritten($fileResource)) {
            $this->createFile($fileResource);
            $this->output->writeln('File "'.$fileResource->getFileName().'" created.');
        } else {
            $this->output->writeln('No file created.');
        }
    }

    protected function fileNotExistsOrShouldBeOwerwritten(FileResource $fileResource): bool
    {
        if (false === file_exists($fileResource->getFileName())) {
            return true;
        }

        return $this->askOverwriteConfirmationQuestion();
    }

    protected function askForClassName()
    {
        $question = new Question($this->getQuestionHelper()->getQuestion('Enter class name', ''));
        $question->setAutocompleterValues($this->getExistingNamespaces());

        return $this->askQuestion($question);
    }

    protected function askForParameterClassName()
    {
        $question = new Question($this->getQuestionHelper()->getQuestion('Enter parameter class name', ''));
        $question->setAutocompleterValues($this->getExistingClasses());

        return $this->askQuestion($question);
    }

    protected function askForParameterName(string $suggestedName)
    {
        $questionName = new Question(
            $this->getQuestionHelper()->getQuestion('Enter parameter name', $suggestedName),
            $suggestedName
        );

        return $this->askQuestion($questionName);
    }

    protected function askOverwriteConfirmationQuestion()
    {
        $question = new ConfirmationQuestion('File exists, overwrite?', false);

        return $this->askQuestion($question);
    }

    protected function askQuestion(Question $question)
    {
        return $this->getQuestionHelper()->ask($this->input, $this->output, $question);
    }

    protected function createSpecSource(ImprovedClassSource $classSource): ImprovedClassSource
    {
        $generator = new SpecGenerator(new ClassSourceFactory());

        return $generator->generate($classSource);
    }

    protected function getFileResource(ImprovedClassSource $classSource): FileResource
    {
        $factory = new FileFactory($this->getPaths());

        return $factory->create($classSource);
    }

    protected function createFile(FileResource $fileResource)
    {
        $fileGenerator = new FileGenerator(new Filesystem(), PhpParserGeneratorFactory::create());

        $fileGenerator->create($fileResource);
    }

    protected function createClassFromParameterClassName(string $name): Type
    {
        return TypeFactory::createFromName($name);
    }

    protected function getExistingNamespaces(): array
    {
        if (null === $this->existingNamespaces) {
            $sourceCodePathReader = new SourceCodePathReader();

            $this->existingNamespaces = $sourceCodePathReader->getExistingPaths($this->getPaths());
        }

        return $this->existingNamespaces;
    }

    protected function getExistingClasses(): array
    {
        if (null === $this->existingClasses) {
            $sourceCodePathReader = new SourceCodePathReader();

            $this->existingClasses = $sourceCodePathReader->getExistingClasses($this->getPaths());
        }

        return $this->existingClasses;
    }

    protected function getPaths()
    {
        if (null === $this->paths) {
            $pathsGenerator = new Paths($this->getConfig());

            $this->paths = $pathsGenerator->getList();
        }

        return $this->paths;
    }

    private function getConfig()
    {
        $path = getcwd().'/skeleton.yml';

        if (!is_file($path)) {
            return $this->getDefaultConfig();
        }

        $content = file_get_contents($path);

        $config = Yaml::parse($content);

        return $config['nulldev_skeleton'];
    }

    private function getDefaultConfig()
    {
        return [
            'code' => [
                'path'          => 'src/',
                'prefix'        => '',
                'autoload_type' => 'psr4',
            ],
            'phpspec' => [
                'path'          => 'spec/',
                'prefix'        => 'spec\\',
                'autoload_type' => 'psr4',
                'enabled'       => true,
            ],
            'phpunit' => [
                'path'          => 'tests/',
                'prefix'        => 'tests\\',
                'autoload_type' => 'psr4',
                'enabled'       => true,
            ],
        ];
    }

    protected function createGenerator()
    {
        throw new PendingException();
    }

    protected function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper') {
            $question = new QuestionHelper();
            $this->getHelperSet()->set($question);
        }

        return $question;
    }

    abstract protected function getSectionMessage();

    abstract protected function getIntroductionMessage();
}
