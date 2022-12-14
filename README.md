# Todo Or Die

A deadly reminder for your code rot.

Replace your `// @todo` comments with something more actionable. This library will help put those more in your face when the time is right. Yes, "Or Die" means **an exception will be thrown** if your condition is met.


## WHY??

I know what you're thinking: Why would anyone want to purposefully cause a failure?

The main idea is that you should hit these in your testing, not in production. But they *will* be in production, and there's nothing like a little fire under your seat to get things done. (but keep reading for how not to break production)

Yes, it is a bit harsh, but try a `grep -ri '@todo' . | wc -l` on your codebase and see how many are just sitting around being ignored. Face it, [Later equals Never](http://on-agile.blogspot.com/2007/04/why-you-wont-fix-it-later.html). No one is looking at these `todo`s and your code is just rotting away.

Put a real, actionable deadline on those "for now"s and "after next version"s.


## Installation

`composer require robertology/todo_or_die`

For `PHP 7.4`, install version 1, and read the README of that version for usage instructions.

(`composer require robertology/todo_or_die:^1`)


## Usage


`(string $todo_message, bool|Check $check, callable $callable_for_alerting = null)`

#### Modes of Use

1. Die
```php
new Todo($todo_message, $check_to_die);
```

2. Alert
```php
new Todo($todo_message, $check_to_alert, $callable_for_alerting);
```

3. Die or Alert
```php
(new Todo($todo_message, $check_to_die))
  ->alertIf($check_to_alert, $callable_for_alerting);
```

#### Don't Die

Ensure the "Or Die" part never happens by setting the environment variable `TODOORDIE_SKIP_DIE` to a `truthy` value. This will cause only Alerts to be triggered. Any Die condition is ignored. (Hint: this might be a smart move for production)
```php
putenv('TODOORDIE_SKIP_DIE=1');
```

#### Alert Throttling

To avoid saturating your alert systems, throttling is built in (for Alerts only, not for Die). Each `Todo` should alert only once per hour. Change that by setting the environment variable `TODOORDIE_ALERT_THRESHOLD` to the number of seconds desired. Disable throttling by setting this to zero.
```php
// Once per day
putenv('TODOORDIE_ALERT_THRESHOLD=86400');
```
```php
// Disabled (alert every time)
putenv('TODOORDIE_ALERT_THRESHOLD=0');
```


## Examples

```php
use Robertology\TodoOrDie\Todo;
use Robertology\TodoOrDie\Check\Dates as DateCheck;

// Die only
new Todo(
  'Remove after the old jobs have attritioned out',
  new DateCheck(strtotime('1 jan 2024'))
);

// Alert only
// Note the use of fromString() which gives the same result as strtotime() above
new Todo(
  'Remove after the old jobs have attritioned out',
  DateCheck::fromString('1 jan 2024'),
  [$logger, 'warning']
);

// A couple Alerts before we Die
(new Todo('Remove after the old jobs have attritioned out', DateCheck::fromString('1 jan 2024')))
  ->alertIf(DateCheck::fromString('22 dec 2023'), [$logger, 'warning'])
  ->alertIf(time() >= strtotime('27 dec 2023'), $my_slack_callable);
//           ^ the constructor and alertIf() can take a Check object or boolean
```


## Notes

- I used `strtotime()` in the examples for readability, but *do not* use relative dates such as `strtotime('+2 months')` because it will be evaluated each time and "two months from now" will never come when "now" keeps moving.

- The Alert throttling uses a file placed in PHP's temp directory (`sys_get_temp_dir()`) which may be prone to garbage collection now and then, but is likely to work fine for this.

- The name was ([also](https://github.com/davidpdrsn/todo-or-die/blob/a23d80b2ff1cef336cd261380a77a5391377aa26/README.md?plain=1#L24)) shamelessly stolen from the ruby gem [`searls/todo_or_die`](https://github.com/searls/todo_or_die).

- Tip o' the Hat to [The Changelog](https://changelog.com) ([Episode 463](https://changelog.com/podcast/463)) where I first heard of the idea.

### Personal Quest

In addition to having an actual working tool, I had a few goals for myself with this project.

1. Full TDD (Test Driven Development)
    - The tests are a mix of Behavior and Functional tests, and I'm OK with that for this small project.
    - I'm not concerned with 100% coverage, but all behavior should have a test.
2. SOLID "To The Extreme"
    - After getting to a working version, I continued to refactor with an eye on SOLID principles.
    - Such a simple concept could have been written with a single class, but being strict here was a good exercise in defining the *Single* Responsibility.


## License

This project is licensed under the [MIT license](LICENSE).
