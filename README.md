<h1 align="center">RuneterraPHP</h1>

PHP library made for decoding deckcodes and encoding decks for [**Legends of Runeterra**](http://playruneterra.com).  
This Library is mostly based on [**SwitchbladeBot/runeterra**](https://github.com/SwitchbladeBot/runeterra) and was translated into PHP.

### Install
```
composer require mike-reinders/runeterra-php
```

### Usage:
```
DeckEncoding::decode("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA");
```
#### Result:
```
[
  [ 0 => "01IO003", 1 => 3 ], // CardCode, Count
  [ 0 => "01IO013", 1 => 3 ],
  [ 0 => "01IO027", 1 => 3 ],
  [ 0 => "01IO049", 1 => 3 ],
  [ 0 => "01SI007", 1 => 3 ],
  [ 0 => "01SI010", 1 => 3 ],
  [ 0 => "01SI021", 1 => 3 ],
  [ 0 => "01SI023", 1 => 3 ],
  [ 0 => "01SI034", 1 => 3 ],
  [ 0 => "01SI047", 1 => 3 ],
  [ 0 => "01SI049", 1 => 3 ],
  [ 0 => "01SI042", 1 => 2 ],
  [ 0 => "01IO009", 1 => 2 ],
  [ 0 => "01SI015", 1 => 1 ],
  [ 0 => "01IO007", 1 => 1 ],
  [ 0 => "01IO032", 1 => 1 ]
]
```

### RawCodes Usage: (same DeckCode)
```
DeckEncoding::decode("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA", false); // 2nd param default: true
```
#### Result:
```
[
  [
    0 => 1,  // set
    1 => 2,  // faction id
    2 => 3,  // card number
    3 => 3   // card count
  ]
  ...
]
```


### Run Tests (php7.2-cli required)
```
composer install --dev
./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
```