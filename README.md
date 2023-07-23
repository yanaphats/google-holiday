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
$holiday->inCountry('US');

# use year
$holiday->inYear(2017);

# use date between
$holiday->from('2017-01-01');
$holiday->to('2017-12-31');

# with minimal output
$holiday->withMinimalOutput();

# with date only output
$holiday->withDateOnlyOutput();

# get holidays
$holiday->get();
```
