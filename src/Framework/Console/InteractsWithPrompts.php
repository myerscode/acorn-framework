<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console;

use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

trait InteractsWithPrompts
{
    protected ConsoleInputInterface|InputInterface $input;

    protected DisplayOutputInterface $output;

    /**
     * Prompt the user for text input with autocomplete suggestions.
     *
     * @param string[] $choices
     */
    public function anticipate(string $question, array $choices, ?string $default = null): ?string
    {
        $prompt = new Question($this->formatQuestion($question, $default), $default);
        $prompt->setAutocompleterValues($choices);

        /** @var string|null $answer */
        $answer = $this->getQuestionHelper()->ask($this->input, $this->output, $prompt);

        return $answer;
    }

    /**
     * Prompt the user for text input.
     */
    public function ask(string $question, ?string $default = null): ?string
    {
        /** @var string|null $answer */
        $answer = $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            new Question($this->formatQuestion($question, $default), $default),
        );

        return $answer;
    }

    /**
     * Prompt the user to choose from a list of options.
     *
     * @param string[] $choices
     * @return string|string[]
     */
    public function choice(string $question, array $choices, ?string $default = null, bool $multiple = false): string|array
    {
        $prompt = new ChoiceQuestion($this->formatQuestion($question, $default), $choices, $default);

        if ($multiple) {
            $prompt->setMultiselect(true);
        }

        /** @var string|string[] $answer */
        $answer = $this->getQuestionHelper()->ask($this->input, $this->output, $prompt);

        return $answer;
    }

    /**
     * Prompt the user for confirmation (yes/no).
     */
    public function confirm(string $question, bool $default = true): bool
    {
        $defaultText = $default ? 'yes' : 'no';

        /** @var bool $answer */
        $answer = $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            new ConfirmationQuestion($this->formatQuestion($question, $defaultText), $default),
        );

        return $answer;
    }

    /**
     * Prompt the user for input without displaying what they type (passwords, tokens, etc).
     */
    public function secret(string $question): ?string
    {
        $prompt = new Question($this->formatQuestion($question));
        $prompt->setHidden(true);
        $prompt->setHiddenFallback(false);

        /** @var string|null $answer */
        $answer = $this->getQuestionHelper()->ask($this->input, $this->output, $prompt);

        return $answer;
    }

    private function formatQuestion(string $question, ?string $default = null): string
    {
        if ($default !== null) {
            return sprintf(' %s [<comment>%s</comment>]: ', $question, $default);
        }

        return sprintf(' %s: ', $question);
    }

    private function getQuestionHelper(): QuestionHelper
    {
        return new QuestionHelper();
    }
}
