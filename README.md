# RuneterraPHP

A PHP library based on [**SwitchbladeBot/runeterra**](https://github.com/SwitchbladeBot/runeterra) developed for  
decoding deckcodes and encoding decks for [**Legends of Runeterra**](http://playruneterra.com).  

[![Build Status](https://secure.travis-ci.org/mike-reinders/runeterra-php.png)](https://travis-ci.org/mike-reinders/runeterra-php) [![Latest Stable Version](https://poser.pugx.org/mike-reinders/runeterra-php/v)](https://packagist.org/packages/mike-reinders/runeterra-php) [![Total Downloads](https://poser.pugx.org/mike-reinders/runeterra-php/downloads)](https://packagist.org/packages/mike-reinders/runeterra-php) [![Latest Unstable Version](https://poser.pugx.org/mike-reinders/runeterra-php/v/unstable)](https://packagist.org/packages/mike-reinders/runeterra-php) [![License](https://poser.pugx.org/mike-reinders/runeterra-php/license)](https://packagist.org/packages/mike-reinders/runeterra-php)

## Install
```
composer require mike-reinders/runeterra-php
```
[Visit Packagist.org **mike-reinders/runeterra-php**](https://packagist.org/packages/mike-reinders/runeterra-php)  
[Download **Composer PHP Dependency Manager**](https://getcomposer.org/download/)

## Guideline

### raw deck
```php
// DeckEncoding
$raw_deck = DeckEncoding::decode("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA");
$deck_code = DeckEncoding::encode($raw_deck);

// or DeckEncodingFactory
$raw_deck = DeckEncodingFactory::toRawDeck("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA");
$deck_code = DeckEncodingFactory::fromRawDeck($raw_deck);
```

##### return format
```php
$raw_deck = [
  [
    0 => 1,  // set
    1 => 2,  // faction id
    2 => 3,  // card number
    3 => 3   // card count
  ],
  ...
];
```

### card code deck
```php
// DeckEncodingFactory
$card_code_deck = DeckEncodingFactory::toCardCodeDeck("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA");
$deck_code = DeckEncodingFactory::fromCardCodeDeck($card_code_deck);
```

##### return format
```php
$card_code_deck = [
  "01IO003" => 3, // card code => count
  "01IO013" => 3,
  "01SI049" => 3,
  "01SI042" => 2,
  "01IO009" => 2,
  "01SI015" => 1,
  ...
];
```

### card deck
```php
// DeckEncodingFactory
$card_deck = DeckEncodingFactory::toCardDeck("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA", Card::class);
$deck_code = DeckEncodingFactory::fromCardDeck($card_deck);
```

##### return format
```php
$card_deck = [
  Card {
    #set: 1
    #faction_id: 2
    #number: 3
    #count: 3
  },
  Card {
    #set: 1
    #faction_id: 2
    #number: 13
    #count: 3
  },
  Card {
    #set: 1
    #faction_id: 2
    #number: 27
    #count: 3
  },
  Card {
    #set: 1
    #faction_id: 2
    #number: 49
    #count: 3
  }
  ...
];
```

## Library Tests

### PHP-CLI
```shell script
composer install --dev
./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
```

### Docker
##### Download & Install
- Docker Desktop CE for [Windows](https://hub.docker.com/editions/community/docker-ce-desktop-windows) or [Mac](https://hub.docker.com/editions/community/docker-ce-desktop-mac/)  
- Docker Engine CE for [Ubuntu](https://hub.docker.com/editions/community/docker-ce-server-ubuntu), [Debian](https://hub.docker.com/editions/community/docker-ce-server-debian) or [install manually](https://docs.docker.com/engine/install/)

##### Build Image
```shell script
docker build --no-cache -t runeterra-php:latest ./docker
```

##### Test it!
```shell script
# simply run
docker run --name=runeterra-php --rm -it runeterra-php test

# watch: Linux Shell
docker run --name=runeterra-php --rm --volume "/${PWD}:/repository:rw" -it runeterra-php test watch

# watch: Windows Powershell
docker run --name=runeterra-php --rm --volume "${PWD}:/repository:rw" -it runeterra-php test watch

# watch: Windows Command Prompt (CMD)
docker run --name=runeterra-php --rm --volume "%CD%:/repository:rw" -it runeterra-php test watch
```