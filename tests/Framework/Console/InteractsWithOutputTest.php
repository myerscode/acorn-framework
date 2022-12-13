<?php

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\InteractsWithOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;

class InteractsWithOutputTest extends BaseTestCase
{
    public function testDebug(): void
    {
        [$trait, $output] = $this->createTrait();

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $trait->debug('Test Debug');

        $output->assertOutputEquals(':orange_circle: Test Debug');
    }

    public function testError(): void
    {
        [$trait, $output] = $this->createTrait();

        $trait->error('error');

        $output->assertOutputEquals('[ERROR] error');

        $output->reset();

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $trait->error('error');

        $output->assertOutputEquals('');
    }

    public function testInfo(): void
    {
        [$trait, $output] = $this->createTrait();

        $trait->info('Test Information');

        $output->assertOutputEquals(':blue_circle: Test Information');
    }

    public function testIsDebug(): void
    {
        [$trait, $output] = $this->createTrait();

        $this->assertFalse($trait->isDebug());

        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $this->assertFalse($trait->isDebug());

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $this->assertTrue($trait->isDebug());
    }

    public function testIsQuiet(): void
    {
        [$trait, $output] = $this->createTrait();

        $this->assertFalse($trait->isQuiet());

        $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $this->assertTrue($trait->isQuiet());
    }

    public function testIsVerbose(): void
    {
        [$trait, $output] = $this->createTrait();

        $this->assertFalse($trait->isVerbose());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $this->assertTrue($trait->isVerbose());
    }

    public function testIsVeryVerbose(): void
    {
        [$trait, $output] = $this->createTrait();

        $this->assertFalse($trait->isVeryVerbose());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $this->assertFalse($trait->isVeryVerbose());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $this->assertTrue($trait->isVeryVerbose());
    }

    public function testSuccess(): void
    {
        [$trait, $output] = $this->createTrait();

        $trait->success('Test Success');

        $output->assertOutputEquals(':green_circle: Test Success');
    }

    public function testVerbose(): void
    {
        [$trait, $output] = $this->createTrait();

        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $this->assertEquals(OutputInterface::VERBOSITY_DEBUG, $trait->verbosity());

        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $this->assertEquals(OutputInterface::VERBOSITY_NORMAL, $trait->verbosity());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $this->assertEquals(OutputInterface::VERBOSITY_VERBOSE, $trait->verbosity());
    }

    public function testVerbosity(): void
    {
        [$trait, $output] = $this->createTrait();


        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $trait->verbose('Test Verbose Message');

        $output->assertOutputEquals('');

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $trait->verbose('Test Verbose Message');

        $output->assertOutputEquals('Test Verbose Message');
    }

    public function testVeryVerbose(): void
    {
        [$trait, $output] = $this->createTrait();

        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $trait->veryVerbose('Test Very Verbose Message');

        $output->assertOutputEquals('');

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $trait->veryVerbose('Test Very Verbose Message');

        $output->assertOutputEquals('');

        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $trait->veryVerbose('Test Very Verbose Message');

        $output->assertOutputEquals('Test Very Verbose Message');
    }

    private function createTrait(): array
    {
        $trait = new class {
            use InteractsWithOutput;
        };

        $output = $this->createStreamOutput();

        $this->setObjectProperties($trait, ['output' => $output]);

        return [$trait, $output];
    }
}
