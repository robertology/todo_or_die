# Todo Or Die

A deadly reminder for your code rot.


## Installation

`composer require robertology/todo_or_die`


## WHY??

I know what you're thinking: Why would anyone want to purposefully cause a failure?

The main idea is that you should hit these in your testing, not in production. But they will be in production, and there's nothing like a little fire under your seat to get things done. (but keep reading for how not to break production)

Yes, it is a bit harsh, but try a `grep -ri '@todo' . | wc -l` on your codebase and see how many are just sitting around being ignored. Face it, [Later equals Never](http://on-agile.blogspot.com/2007/04/why-you-wont-fix-it-later.html). No one is looking at these `todo`s and your code is just rotting away.

Put a deadline on your "for now"s and "after next version"s.


## Usage

The constructor has two parameters (both required):
1. Your "to do" message
2. A boolean condition to indicate it's past the time to do it
```php
use Robertology\TodoOrDie\Todo;
new Todo('Remove this hack after the old ones have been processed', time() > strtotime('1 jan 2024'));
```

You can chain more conditions. The first one to evaluate as `true` will "or die" your code.
```php
(new Todo(…))
  ->orIf(version_compare(phpversion(), '8.1', '>'))
  ->orIf($some_other_condition);
```

You can also add a callable to act as an alert - to help avoid the "or die" by reminding you to do it soon. This has two parameters (both required):
1. The boolean condition
2. A callable to do the alert

These can also be chained. Any and all that evaluate to `true` will trigger the callable.
The callable will be called with the "to do" message.
```php
(new Todo(…))
  ->alertIf(time() > strtotime('1 dec 2023'), [$logger, 'debug'])
  ->alertIf($some_condition, $my_callable);
```

### Don't Die

Fine, you don't want to deal with things actually failing on you. There are a few ways to make that happen.

#### Globally

1. Set the `TODOORDIE_SKIP_DIE` environment variable to a truthy value. This will skip the "or die" for all `Todo`s but will still evaluate the `alertIf()` conditions. This is a good option for production.
```php
putenv('TODOORDIE_SKIP_DIE=1');
```


2. Extend the `Todo` class and override the `protected _die()` method to do whatever you want it to do. Note: `_die()` will only be called once, when the first "die" condition is met Also, none of the alerts will be triggered after that.

#### Case by case
1. Give your alert callable as the third argument to the constructor. This will convert the condition to an alert only.
```php
new Todo('Zhu Li, do the thing', true, $callable);
```

2. Make the initial condition be `false` and add an alert. But this is silly; just do it the other way.
```php
(new Todo('Zhu Li, do the thing', false))->alertIf(…);
```


## Notes

I used `strtotime()` in the examples but *do not* use relative dates such as `strtotime('+2 days')` because it will be evaluated each time and "two days from now" will never come.

The name was shamelessly stolen from the ruby gem [`todo_or_die`][ruby].


## License

This project is licensed under the [MIT license](LICENSE).
