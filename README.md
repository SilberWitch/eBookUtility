# Alexandria Upload Utility

## Description

This is a simple PHP program that takes an edited/pre-formatted Asciidoc document, splits it into the specific sections or chapters defined (based upon the first and second level headers, for further levels, please use the [Alexandria client](https://gitcitadel.com/r/naddr1qvzqqqrhnypzplfq3m5v3u5r0q9f255fdeyz8nyac6lagssx8zy4wugxjs8ajf7pqy2hwumn8ghj7un9d3shjtnyv9kh2uewd9hj7qq2g9kx27rpdej8y6tpq7z4jt)), generates a [curated publication](https://github.com/nostr-protocol/nips/pull/1600) from that file, and writes it to the relays selected.

## Prerequisites

You will need to have php (including php-cli) and composer installed on the machine and configured.
Then run ```composer install``` to download the dependencies to a *vendor* folder and create the *composer.lock* file.

## Directions

1. Save your Bech32 nsec in the environment variable with `export NOSTR_SECRET_KEY=nsec123`.
2. Open the folder *user* and edit the file *relays.yml* containing your list of relays. We recommend keeping wss://thecitadel.nostr1.com in your list and adding at least one other, that you have write access to. If you remove all relays, the Citadel relay will be used as default. ```a``` tags will always contain thecitadel relay as relay hint.
3. Decide whether you want your 30040 index to contain ```e``` tags or the newer ```a``` tag version (according to [NKBIP-01](https://wikistr.com/nkbip-01*fd208ee8c8f283780a9552896e4823cc9dc6bfd442063889577106940fd927c1)). This is denoted by adding the corresponding letter to the end of the command line arguments. I recommend using ```a```.
4. Return to the main/upper folder, create an Asciidoc file entitled something like *MyShortBookTitle.adoc* and have it formatted with precisely two levels of headers.

```
= title you want as the full book title (mind the space after the equal sign)

== topic1

text that you want displayed as content

== topic2

more text
```

5. On the command line, enter 

```php createBook.php MyShortBookTitle.adoc "Author Name" "book version" a```

6. Make sure to replace the filename with the file you want to use and the author name with the name or npub of the person/entity that should be listed as the author in the 30040 event. The book version is the edition, translation, etc. of the book. The _a_ denotes that you want the replaceable ```a``` tags.
7. All of the event metadata will be added to the *eventsCreated.txt* file.
8. The 30040 eventID will be sent to stdout (usually the command line) in the form of an njump hyperlink. The link will not work, if you wrote to a local relay, but you can still see the eventID.

## Integration Test

To check that everything is installed correctly, you can run 

```
./vendor/bin/phpunit src/IntegrationTest.php
```

to see the integration test, if you have PHPUnit installed.