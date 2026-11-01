# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Calendar Versioning](https://calver.org/).

## [Unreleased]

### Added
- Dependency resolution for commands: `$this->resolve(Service::class)` and constructor injection (the container autowires discovered commands); `Container::has()`.
- `DisplayOutput::yaml()` and `DisplayOutput::csv()` output formatters alongside `json()`.
- `Command::fail()` to abort a command with a chosen exit code, `callSilently()` to run a sub-command with output suppressed, and an `InteractsWithSignals` trait for graceful SIGINT/SIGTERM handling of long-running commands.
- `DisplayOutput::title()` for a title bar.
- Configurable CLI branding: the application name and version come from the `app.name` / `app.version` config keys (defaulting to the framework's own).
- Typed input accessors: `integerArgument`, `floatArgument`, `booleanArgument`, `integerOption`, `floatOption` (plus the existing `flag()` for boolean switches).
- `DisplayOutput::json()` for machine-readable output (pretty by default, compact for piping).
- `acorn:optimize` / `acorn:clear` commands and a cached discovery manifest, so command, provider and listener discovery can skip the per-boot filesystem scan and reflection.
- `Terminal::runShell()` as an explicit, clearly-named alias for the shell-based `run()`.
- Working queue support: a real `Worker` that drains a queue and a deferred in-process `MemoryQueue` (the run-on-push `SynchronousQueue` remains the default).
- `Command::flag()` for reading boolean switch options.

### Changed
- Listener failures during dispatch are now logged (via the application logger) instead of being silently swallowed; isolation between listeners is preserved.
- `Listener::handle()` bridges to a listener's `__invoke()` method when present, so either convention works.
- Documentation added for optimization, and updated for the terminal, listeners and cache.

### Fixed
- Emoji shortcodes (`:blue_circle:` etc.) in `info`/`debug`/`success` output are now rendered as emoji instead of printed literally.
- `symfony/yaml` pinned to a patched release (CVE-2026-109413).
- `FileCacheDriver` no longer errors on a corrupt or externally-written cache entry and creates its cache directory privately (0700).
- `Cache::setMultiple()` namespaces keys and normalises a `DateTimeInterface` TTL consistently with `set()`.
- Cache keys are validated per PSR-16 (reserved characters and empty keys are rejected).
- Command and listener discovery verify `class_exists` before probing a candidate.

### Security
- Shell-injection guidance on `Terminal::run()`, steering user-input commands to the argv-based `runSafe()`.
