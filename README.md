# RuneterraPHP

A PHP library based on [**SwitchbladeBot/runeterra**](https://github.com/SwitchbladeBot/runeterra) developed for  
decoding deckcodes and encoding decks for [**Legends of Runeterra**](http://playruneterra.com).  

## Install
```
composer require mike-reinders/runeterra-php
```
[Visit Packagist.org **mike-reinders/runeterra-php**](https://packagist.org/packages/mike-reinders/runeterra-php)  
[Download **Composer PHP Dependency Manager**](https://getcomposer.org/download/)

## Guideline
```php
DeckEncoding::decode("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA", true);
DeckEncoding::decode("CEBAIAICAMGRWMIHAECQOCQVC4RC6MICAEAQKKQBAEBASAQBAECQ6AQBAIDSA", false);
```

#### Result
```php
// Array of counted card codes
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
  [ 0 => "01IO032", 1 => 1 ],
  ...
];

// Array of counted card identifiers
[
  [
    0 => 1,  // set
    1 => 2,  // faction id
    2 => 3,  // card number
    3 => 3   // card count
  ],
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

# watch: Linux Shell / Windows Powershell
docker run --name=runeterra-php --rm --volume "${PWD}:/repository:rw" -it runeterra-php test watch

# watch: Windows Command Prompt (CMD)
docker run --name=runeterra-php --rm --volume "%CD%:/repository:rw" -it runeterra-php test watch
```