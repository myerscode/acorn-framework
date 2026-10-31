<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions;

use Exception;
use InvalidArgumentException;
use Myerscode\Acorn\Foundation\Exceptions\ExceptionHandler;
use Myerscode\Acorn\Framework\Log\NullLogger;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;
use Mockery;

final class ExceptionHandlerTest extends BaseTestCase
{
    public function testCatchHandlerDoesNotMatchUnrelatedExceptions(): void
    {
        $caught = false;

        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(InvalidArgumentException::class, function () use (&$caught): bool {
            $caught = true;

            return true;
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('wrong type'));

        $this->assertFalse($caught);
        $this->assertStringContainsString('wrong type', $output->fetch());
    }

    public function testCatchHandlerMatchesSubclasses(): void
    {
        $caught = false;

        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(Exception::class, function () use (&$caught): bool {
            $caught = true;

            return true;
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('subclass'));

        $this->assertTrue($caught);
    }

    public function testCatchHandlerReceivesException(): void
    {
        $caught = null;

        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(RuntimeException::class, function (RuntimeException $e) use (&$caught): ?bool {
            $caught = $e;

            return true;
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('caught it'));

        $this->assertNotNull($caught);
        $this->assertSame('caught it', $caught->getMessage());
    }

    public function testCatchHandlerReturningFalseAllowsRendering(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(RuntimeException::class, fn (): bool => false);

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('should still render'));

        $this->assertStringContainsString('should still render', $output->fetch());
    }

    public function testCatchHandlerReturningNullAllowsRendering(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(RuntimeException::class, fn (): null => null);

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('null means continue'));

        $this->assertStringContainsString('null means continue', $output->fetch());
    }

    public function testCatchHandlerReturningTruePreventsRendering(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->catch(RuntimeException::class, fn (): bool => true);

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('should not render'));

        $this->assertSame('', $output->fetch());
    }

    public function testDontReportMatchesSubclasses(): void
    {
        $logger = $this->mock(LoggerInterface::class);
        $logger->shouldNotReceive('error');

        $handler = new ExceptionHandler($logger);
        $handler->dontReport(Exception::class);
        $handler->report(new RuntimeException('Subclass of Exception'));

        $this->assertSame(1, 1); // Mockery verifies in tearDown
    }

    public function testDontReportSuppressesLogging(): void
    {
        $logger = $this->mock(LoggerInterface::class);
        $logger->shouldNotReceive('error');

        $handler = new ExceptionHandler($logger);
        $handler->dontReport(RuntimeException::class);
        $handler->report(new RuntimeException('Should not be logged'));

        $this->assertSame(1, 1); // Mockery verifies in tearDown
    }

    public function testMultipleCatchHandlersRunInOrder(): void
    {
        $order = [];

        $handler = new ExceptionHandler(new NullLogger());

        $handler->catch(RuntimeException::class, function () use (&$order): ?bool {
            $order[] = 'first';

            return null;
        });

        $handler->catch(RuntimeException::class, function () use (&$order): bool {
            $order[] = 'second';

            return true;
        });

        $handler->catch(RuntimeException::class, function () use (&$order): bool {
            $order[] = 'third';

            return true;
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('multi'));

        // Third should not run because second returned true
        $this->assertSame(['first', 'second'], $order);
    }

    public function testRenderOutputsExceptionToConsole(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $output = new BufferedOutput();

        $handler->render($output, new RuntimeException('Render test'));

        $display = $output->fetch();
        $this->assertStringContainsString('RuntimeException', $display);
        $this->assertStringContainsString('Render test', $display);
    }

    public function testRenderUsingCallsCustomRenderer(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->renderUsing(RuntimeException::class, function (OutputInterface $output, RuntimeException $e): void {
            $output->writeln('CUSTOM: ' . $e->getMessage());
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('custom render'));

        $this->assertStringContainsString('CUSTOM: custom render', $output->fetch());
    }

    public function testRenderUsingDoesNotMatchUnrelatedExceptions(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->renderUsing(InvalidArgumentException::class, function (OutputInterface $output): void {
            $output->writeln('SHOULD NOT APPEAR');
        });

        $output = new BufferedOutput();
        $handler->render($output, new RuntimeException('different'));

        $display = $output->fetch();
        $this->assertStringNotContainsString('SHOULD NOT APPEAR', $display);
        $this->assertStringContainsString('different', $display);
    }
    public function testReportLogsException(): void
    {
        $logger = $this->mock(LoggerInterface::class);
        $logger->shouldReceive('error')
            ->once()
            ->with('Something broke', Mockery::type('array'));

        $handler = new ExceptionHandler($logger);
        $handler->report(new RuntimeException('Something broke'));

        $this->assertSame(1, 1); // Mockery verifies in tearDown
    }

    public function testReportUsingCallsCustomReporter(): void
    {
        $reported = false;

        $handler = new ExceptionHandler(new NullLogger());
        $handler->reportUsing(RuntimeException::class, function (RuntimeException $e) use (&$reported): void {
            $reported = true;
        });

        $handler->report(new RuntimeException('custom'));

        $this->assertTrue($reported);
    }

    public function testReportUsingDoesNotMatchUnrelatedExceptions(): void
    {
        $reported = false;

        $handler = new ExceptionHandler(new NullLogger());
        $handler->reportUsing(InvalidArgumentException::class, function () use (&$reported): void {
            $reported = true;
        });

        $handler->report(new RuntimeException('different type'));

        $this->assertFalse($reported);
    }

    public function testShouldReportReturnsFalseForSuppressed(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $handler->dontReport(RuntimeException::class);

        $this->assertFalse($handler->shouldReport(new RuntimeException('test')));
    }

    public function testShouldReportReturnsTrueByDefault(): void
    {
        $handler = new ExceptionHandler(new NullLogger());

        $this->assertTrue($handler->shouldReport(new RuntimeException('test')));
    }

    public function testVerboseRenderIncludesFileAndLine(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        $handler->render($output, new RuntimeException('verbose test'));

        $display = $output->fetch();
        $this->assertStringContainsString('ExceptionHandlerTest.php', $display);
    }

    public function testVeryVerboseRenderIncludesTrace(): void
    {
        $handler = new ExceptionHandler(new NullLogger());
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $handler->render($output, new RuntimeException('trace test'));

        $display = $output->fetch();
        $this->assertStringContainsString('Exception trace:', $display);
    }
}
