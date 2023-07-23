** Install **
```
composer require "sandbox-dev/google-holiday"
```

** Set .env **
```
GOOGLE_API_KEY=your_api_key
```

** Usage **
```
use SandboxDev\Google\Holiday;

$holiday = new Holiday();

# use locale
$holiday->locale('en');

# use country
$holiday->country('US');

# use year
$holiday->year(2017);

# use date between
$holiday->from('2017-01-01');
$holiday->to('2017-12-31');

# with simple output
$holiday->simple();

# with date only output
$holiday->dateOnly();

# get holidays
$holiday->get();
```
