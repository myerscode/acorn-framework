<?php

declare(strict_types=1);

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

final class InteractsWithPromptsTest extends BaseTestCase
{
    public function testAnticipateAllowsFreeformInput(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:anticipate-free';

            public function handle(): int
            {
                $name = $this->anticipate('Enter a framework', ['acorn', 'laravel', 'symfony']);
                $this->line('Selected: ' . ($name ?? 'null'));

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['custom-framework']);
        $tester->execute([]);

        $this->assertStringContainsString('Selected: custom-framework', $tester->getDisplay());
    }

    public function testAnticipatePromptsWithSuggestions(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:anticipate';

            public function handle(): int
            {
                $name = $this->anticipate('Enter a framework', ['acorn', 'laravel', 'symfony']);
                $this->line('Selected: ' . ($name ?? 'null'));

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['acorn']);
        $tester->execute([]);

        $this->assertStringContainsString('Selected: acorn', $tester->getDisplay());
    }

    public function testAnticipateWithDefault(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:anticipate-default';

            public function handle(): int
            {
                $name = $this->anticipate('Enter a framework', ['acorn', 'laravel', 'symfony'], 'acorn');
                $this->line('Selected: ' . ($name ?? 'null'));

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['']);
        $tester->execute([]);

        $this->assertStringContainsString('Selected: acorn', $tester->getDisplay());
    }
    public function testAskPromptsForInput(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:ask';

            public function handle(): int
            {
                $name = $this->ask('What is your name');
                $this->line('Hello ' . $name);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['Fred']);
        $tester->execute([]);

        $this->assertStringContainsString('Hello Fred', $tester->getDisplay());
    }

    public function testAskWithDefaultValue(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:ask-default';

            public function handle(): int
            {
                $name = $this->ask('What is your name', 'World');
                $this->line('Hello ' . $name);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['']);
        $tester->execute([]);

        $this->assertStringContainsString('Hello World', $tester->getDisplay());
    }

    public function testChoiceMultipleSelections(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:choice-multi';

            public function handle(): int
            {
                /** @var string[] $colours */
                $colours = $this->choice('Pick colours', ['red', 'green', 'blue'], null, true);
                $this->line('You chose ' . implode(', ', $colours));

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['red,blue']);
        $tester->execute([]);

        $this->assertStringContainsString('You chose red, blue', $tester->getDisplay());
    }

    public function testChoiceSelectsSingleOption(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:choice';

            public function handle(): int
            {
                /** @var string $colour */
                $colour = $this->choice('Pick a colour', ['red', 'green', 'blue']);
                $this->line('You chose ' . $colour);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['green']);
        $tester->execute([]);

        $this->assertStringContainsString('You chose green', $tester->getDisplay());
    }

    public function testChoiceWithDefault(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:choice-default';

            public function handle(): int
            {
                /** @var string $colour */
                $colour = $this->choice('Pick a colour', ['red', 'green', 'blue'], 'blue');
                $this->line('You chose ' . $colour);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['']);
        $tester->execute([]);

        $this->assertStringContainsString('You chose blue', $tester->getDisplay());
    }

    public function testConfirmDefaultValue(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:confirm-default';

            public function handle(): int
            {
                $result = $this->confirm('Do you agree', false);
                $this->line($result ? 'agreed' : 'declined');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['']);
        $tester->execute([]);

        $this->assertStringContainsString('declined', $tester->getDisplay());
    }

    public function testConfirmReturnsFalse(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:confirm-no';

            public function handle(): int
            {
                $result = $this->confirm('Do you agree', false);
                $this->line($result ? 'agreed' : 'declined');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['no']);
        $tester->execute([]);

        $this->assertStringContainsString('declined', $tester->getDisplay());
    }

    public function testConfirmReturnsTrue(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:confirm';

            public function handle(): int
            {
                $result = $this->confirm('Do you agree');
                $this->line($result ? 'agreed' : 'declined');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['yes']);
        $tester->execute([]);

        $this->assertStringContainsString('agreed', $tester->getDisplay());
    }

    public function testSecretHidesInput(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:secret';

            public function handle(): int
            {
                $password = $this->secret('Enter password');
                $this->line('Got: ' . ($password ?? 'null'));

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->setInputs(['s3cret']);
        $tester->execute([]);

        $display = $tester->getDisplay();
        $this->assertStringContainsString('Got: s3cret', $display);
        $this->assertStringNotContainsString('s3cret', str_replace('Got: s3cret', '', $display));
    }
}
