# Todo Or Die

A deadly reminder for your code rot.

Replace your `// @todo` comments with something more actionable. This library will help put those more in your face when the time is right. Yes, "Or Die" means **an exception will be thrown** if your condition is met.


## WHY??

I know what you're thinking: Why would anyone want to purposefully cause a failure?

The main idea is that you should hit these in your testing, not in production. But they *will* be in production, and there's nothing like a little fire under your seat to get things done. (but keep reading for how not to break production)

Yes, it is a bit harsh, but try a `grep -ri '@todo' . | wc -l` on your codebase and see how many are just sitting around being ignored. Face it, [Later equals Never](http://on-agile.blogspot.com/2007/04/why-you-wont-fix-it-later.html). No one is looking at these `todo`s and your code is just rotting away.

Put a real, actionable deadline on those "for now"s and "after next version"s.


## Installation

1. Add the repo to composer

… with a single command:

`composer config repositories.todo_or_die vcs https://github.com/robertology/todo_or_die`

… or manually by adding to (or creating) the `repositories` section in your `composer.json`:
```
"repositories": [
  {"type": "vcs", "url": "https://github.com/robertology/todo_or_die"}
]
```

2. Tell composer to require it for your package

`composer require robertology/todo_or_die`


## Usage


`(string $todo_message, bool $condition, callable $callable_for_alerting = null)`

#### Modes of Use

1. Die
```php
new Todo($todo_message, $condition_to_die);
```

2. Alert
```php
new Todo($todo_message, $condition_to_alert, $callable_for_alerting);
```

3. Die or Alert
```php
(new Todo($todo_message, $condition_to_die))
  ->alertIf($condition_to_alert, $callable_for_alerting);
```

#### Don't Die

Ensure the "Or Die" part never happens by setting the environment variable `TODOORDIE_SKIP_DIE` to a `truthy` value. This will cause only Alerts to be triggered. Any Die condition is ignored. (Hint: this might be a smart move for production)
```php
putenv('TODOORDIE_SKIP_DIE=1');
```

#### Alert Throttling

To avoid saturating your alert systems, throttling is built in (for Alerts only, not for Die). Each `Todo` should alert only once per hour.


## Examples

```php
use Robertology\TodoOrDie\Todo;

// Die only
new Todo(
  'Remove after the old jobs have attritioned out',
  time() > strtotime('1 jan 2024')
);

// Alert only
new Todo(
  'Remove after the old jobs have attritioned out',
  time() > strtotime('1 jan 2024'),
  [$logger, 'warning']
);

// A couple Alerts before we Die
(new Todo('Remove after the old jobs have attritioned out', time() > strtotime('1 jan 2024')))
  ->alertIf(time() > strtotime('22 dec 2023'), [$logger, 'warning'])
  ->alertIf(time() > strtotime('27 dec 2023'), $my_slack_callable);
```


## Notes

- I used `strtotime()` in the examples for readability, but *do not* use relative dates such as `strtotime('+2 months')` because it will be evaluated each time and "two months from now" will never come when "now" keeps moving.

- The Alert throttling uses a file placed in PHP's temp directory (`sys_get_temp_dir()`) which may be prone to garbage collection now and then, but is likely to work fine for this.

- The name was ([also](https://github.com/davidpdrsn/todo-or-die/blob/a23d80b2ff1cef336cd261380a77a5391377aa26/README.md?plain=1#L24)) shamelessly stolen from the ruby gem [`searls/todo_or_die`](https://github.com/searls/todo_or_die).


## License

This project is licensed under the [MIT license](LICENSE).
