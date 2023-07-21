# Laravel CRUD Generator

A simple CRUD generator package. This package will generate 
Controller, Migration, Model, Request, Views and Route through a single command.

## Features

With a single command, this package can generate:
- Migration file will be generated and table will be automatically created in the database.
- Model file will be created with `guarded`.
- Request file will be created with field and validation rules.
- Controller file will be created with all the function with operations.
- Views files will be generated in a folder with the crud name specified in the command.
- Resource Route will be generated in the `web.php` file.

## Requirements
    Laravel >= 8.0
    PHP >= 7.4

## Command

```
php artisan make:crud Product --fields='name#string; description#text; price#float; type#select#options={"Book": "Book", "Food": "Food", "Medicine": "Medicine", "Furniture": "Furniture"}'
```

## Field Types
Currently these following field types work for this package.

- string
- text
- mediumtext
- longtext
- password
- email
- number
- integer
- bigint
- mediumint
- tinyint
- smallint
- decimal
- double
- float
- select

Form input type, validation rule and sql column type are generated based on the field types.

## Template

Currently view files are generating using Bootstrap by default.

## Incomplete things

- There is a problem in the select field to parse value. It will be solved in the next version.
- Could not write all the tests because of short time.
