# Pdfparse
PHP tools to find the coordinates (x,y) of a word in a pdf file.
## Requirement
- Php version >= 5.6
- Enable shell_exec
- bin folder writable recursive

## Usage
``` php
<?php
include 'Pdfparse.php';
$pdfFile = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'sample.pdf';
$pdfParse = new Pdfparse($pdfFile);
$result1 = json_encode($pdfParse->findText(array('lorem','ipsum')));
$result2 = json_encode($pdfParse->findText('dolor'));

echo $result2;
?>
```
## Output
``` json
{
  "page": {
    "page1": {
      "width": "594",
      "height": "841"
    },
    "page2": {
      "width": "594",
      "height": "841"
    }
  },
  "content": {
    "dolor": [
      {
        "text": "dolorem",
        "x": "142",
        "y": "174",
        "page": 1
      },
      {
        "text": "dolor",
        "x": "72",
        "y": "463",
        "page": 1
      },
      {
        "text": "dolor",
        "x": "413",
        "y": "491",
        "page": 1
      },
      {
        "text": "dolor",
        "x": "72",
        "y": "724",
        "page": 1
      },
      {
        "text": "dolor",
        "x": "72",
        "y": "734",
        "page": 1
      },
      {
        "text": "dolore",
        "x": "72",
        "y": "744",
        "page": 1
      },
      {
        "text": "doloremque",
        "x": "72",
        "y": "789",
        "page": 1
      },
      {
        "text": "dolores",
        "x": "72",
        "y": "30",
        "page": 2
      },
      {
        "text": "dolorem",
        "x": "72",
        "y": "40",
        "page": 2
      },
      {
        "text": "dolore",
        "x": "72",
        "y": "50",
        "page": 2
      },
      {
        "text": "dolorem",
        "x": "72",
        "y": "70",
        "page": 2
      },
      {
        "text": "dolores",
        "x": "72",
        "y": "210",
        "page": 2
      },
      {
        "text": "dolorum",
        "x": "72",
        "y": "230",
        "page": 2
      },
      {
        "text": "dolor",
        "x": "72",
        "y": "240",
        "page": 2
      },
      {
        "text": "doloribus",
        "x": "72",
        "y": "270",
        "page": 2
      }
    ]
  }
}
```

## Powered by
- pdftohtml
```
pdftohtml version 4.02
Copyright 1996-2019 Glyph & Cog, LLC
```