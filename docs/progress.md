# Progress Indicators

Commands can display progress to users during long-running operations. These methods are available on any class extending `Command`.

## createProgressBar

Create a progress bar with a known number of steps:

```php
$bar = $this->createProgressBar(100);
$bar->start();

foreach ($items as $item) {
    $this->processItem($item);
    $bar->advance();
}

$bar->finish();
$this->output->writeln('');
```

The returned `ProgressBar` is a standard Symfony Console progress bar — you can customise its format, redraw frequency, and more.

## withProgressBar

Iterate over a collection while automatically advancing a progress bar:

```php
$this->withProgressBar($users, function (User $user, ProgressBar $bar) {
    $this->sendNotification($user);
});
```

The progress bar starts, advances once per item, and finishes automatically.

## withSpinner

Display a message while executing a callback. Useful for indeterminate tasks where you don't know how many steps there will be:

```php
$result = $this->withSpinner(function () {
    return $this->fetchRemoteData();
}, 'Fetching data...');
```

Outputs:
```
Fetching data... done.
```

The callback's return value is passed through.

## withTask

Execute a callback with a status message and elapsed time reporting:

```php
$this->withTask('Compiling assets...', function () {
    $this->compileAssets();
});
```

Outputs:
```
Compiling assets... (1.23s)
```

The callback's return value is passed through:

```php
$result = $this->withTask('Building index...', function () {
    return $this->buildIndex();
});
```
