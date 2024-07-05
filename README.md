# Alexandria Upload Utility

## Description

This is a simple PHP program that takes an edited/pre-formatted markdown document, splits it into the specific sections or chapters defined (based upon the first and second level headers) and then generates an Nostr eBook or "note collection" from that file and writes it to the relays selected.

## Prerequisites

You will need to have php (including php-cli) and composer installed on the machine and configured.
Then run ```composer install``` to download the dependencies to a *vendor* folder and create the *composer.lock* file.

## Directions

1. Open the folder *user*, create the file *nostr-private.key* and add your nsec to it.
2. Then edit the file *relays.yml* containing your list of relays. We recommend keeping wss://thecitadel.nostr1.com in your list and adding at least one other, that you have write access to. If you remove all relays, the Citadel relay will be used as default.
3. Return to the main/upper folder, create a markdown file entitled something like *MyShortBookTitle.md* and have it formatted with precisely two levels of headers, like so:
```
# title you want as the full book title (mind the space after the hashtag)
## topic1
text that you want displayed as content
## topic2
more text
```
4. On the command line, write ```php createBook.php MyShortBookTitle.md "Author Name"``` and press Enter. Make sure to replace the filename with the short title you want to use and the author name with the name or npub of the person/entity that should be listed as the author in the 30040 event.
5. All of the event metadata will be added to the *eventsCreated.txt* file.
6. The 30040 eventID will be sent to stdout (usually the command line) in the form of an njump hyperlink. The link will not work, if you wrote to a local relay, but you can still see the eventID.