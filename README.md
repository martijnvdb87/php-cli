# PHP CLI
This library help you to create CLI applications quicky.

## Installation
You can install the package via composer:
```php
composer require martijnvdb/php-cli
```

## Usage
Add the composer autoloader to your application and create a new instance of the CLI class. To run the application, use the `run()` method. In this example will be place this in a file called `myapp` (without the `.php` extension).
```php
require __DIR__ . '/vendor/autoload.php';

use Martijnvdb\PhpCli\Cli;

$cli = new Cli('First CLI App', '0.1.0')->run();
```
To add a default command to the application use the `add()` method. Use a callback function as the first argument. This callback will be called without giving any extra argument. To execute the script, run `php myapp`.
```php
$cli = new Cli('First CLI App', '0.1.0');

$cli->add(function ($input, $output) {
        // ...
})
->run();
```
The `add()` method can also be used to register a command to your application. The example will be executed by running `php myapp helloworld`.
```php
$cli = new Cli('First CLI App', '0.1.0');

$cli->add('helloworld', function ($input, $output) {
        // ...
})
->run();
```

## Using the $options variable
The `$options` object will contain all the command line arguments. You can retrieve the values using the `all()` and `get()` methods. The example will return the following while running `php myapp helloworld --message "Hello, World!"'`.

```php
$cli->add('helloworld', function ($options, $output) {
    // This will return all options
    $options = $options->all();
    
    // This will return the value of the '--message' option
    $message = $options->get('--message'); 
    
    // Will return the value of the '--message' or '-m' option
    $message = $options->get('--message', '-m');
 
    // $options = ["--message" => "Hello, World!"];
    // $message = "Hello, World!";
})
```

## Using the $output variable
The `$output` object will help you formatting your output.

### BB Code
This library support a custom version of BB code to help you style your output. You can mix and match any tags as long as they start with an opening and closing tag.
```php
$output->line('[bold]Bold Text[/bold]');
$output->line('[red]Red Text[/red]');
$output->line('[bg:green]Green Background[/bg:green]');
$output->line('[bg:white][magenta][italic]Italic magenta text on a white background[/italic][/magenta][/bg:white]');
```

##### Styling
These will change the style of the text.

`[bold]`, `[b]`, `[dim]`, `[italic]`, `[i]`, `[underline]`, `[u]`, `[blink]`, `[inverse]`, `[reverse]`, `[invisible]`, `[strikethrough]`, `[s]`

##### Text colors
These will change the color of the text.

`[black]`,`[red]`,`[green]`,`[yellow]`,`[blue]`,`[magenta]`,`[cyan]`,`[white]`

##### Background colors
These will change the background color of the text.

`[bg:black]`, `[bg:red]`, `[bg:green]`, `[bg:yellow]`, `[bg:blue]`, `[bg:magenta]`, `[bg:cyan]`, `[bg:white]`

### $output Methods
The `$output` object contains the following methods:

#### Basic text
Four basis text output methods. The only difference between them is how the handle new lines.
```php
$output->echo(string $value = '');
$output->line(string $value = '');
$output->lines(array $lines = []);
$output->paragraph(string $value = '');
```

#### Version
Output the current version of the application.
```php
$output->version();
```

#### Columns
Output a formatted column. The `$rows` variable is an array containing arrays in which each entry is a cell. The `$column_styles` variable allows you to style a full column in the same style. You should use the BB code without brackets for this.
```php
$output->columns(string $label, array $rows = [], array $column_styles = []);
```

Take a look at `examples/generate` to see some examples of how to use these methods.

## Input Helper Class
This library also contains a helper class which allows the application to easy get a specific input of the user.

```php
$input = Input::text(string $label)->get();
$input = Input::number(string $label)->get();
$input = Input::choice(string $label, array $options)->get();
```

Example of the choice input helper:
```php
$input = Input::choice('[yellow]Select an option[/yellow] [green](1/2/3)[/green]:', [
        '1' => 'Option 1',
        '2' => 'Option 2',
        '3' => 'Option 3'
    ])
    ->required()
    ->setDefault('1')
    ->get();
```

### Input methods

#### Force the use to give an input
If the given input is empty, then an required message will show up.
```php
$input->required(string $message);
```

#### Set a default
If the user doesn't give an input, the given default value will be returned.
```php
$input->setDefault($default);
```

#### Set the invalid message
The message that will show up when the user enters an invalid input.
```php
$input->setInvalidMessage(string $message);
```

#### Set the input styling
The `$options` array can be filled with BB code tags without the brackets (eg. `['red', 'bold']`).
```php
$input->inputStyling($options = []);
```