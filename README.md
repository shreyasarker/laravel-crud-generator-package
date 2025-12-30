# Laravel CRUD Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shreyasarker/lara-crud.svg?style=flat-square)](https://packagist.org/packages/shreyasarker/lara-crud)
[![Total Downloads](https://img.shields.io/packagist/dt/shreyasarker/lara-crud.svg?style=flat-square)](https://packagist.org/packages/shreyasarker/lara-crud)
[![License](https://img.shields.io/packagist/l/shreyasarker/lara-crud.svg?style=flat-square)](https://packagist.org/packages/shreyasarker/lara-crud)

Generate complete CRUD operations for your Laravel application with a single command. Build migrations, models, controllers, requests, views, and routes in seconds with an interactive wizard.

## ✨ Features

- 🎯 **Interactive Field Wizard** - Define fields with types, validation, and options through a guided CLI
- 🎨 **Multiple UI Stacks** - Choose between Bootstrap 5 or Tailwind CSS for generated views
- 🔄 **Web & API Support** - Generate standard web controllers or API-only controllers
- 📝 **Smart Model Generation** - Auto-generated fillable, casts, and hidden properties
- ✅ **Form Request Validation** - Custom validation messages and attribute names
- 🎭 **Nullable Support** - Automatically detects and applies nullable to migrations and models
- 🔧 **Flexible Options** - Generate only what you need with `--only` and `--skip` options
- 🧪 **Dry Run Mode** - Preview what will be generated before creating files
- 💪 **Force Overwrite** - Update existing files with `--force` flag

## 📋 Requirements

- PHP 8.2 or higher
- Laravel 10.x, 11.x, or 12.x

## 📦 Installation

Install via Composer:

```bash
composer require shreyasarker/lara-crud --dev
```

The package will automatically register itself.

## 🚀 Quick Start

Generate a complete CRUD with the interactive wizard:

```bash
php artisan make:crud Post --interactive
```

Follow the prompts to add fields:

```
Field name: title
Type: string
Nullable? No
Validation rules: required|string|max:255

Field name: content
Type: text
Nullable? Yes
Validation rules: nullable|string

Field name: is_published
Type: boolean
Nullable? No
Validation rules: required|boolean

Add another field? No
```

This generates:
- ✅ Migration (`xxxx_create_posts_table.php`)
- ✅ Model (`Post.php`) with fillable, casts, and hidden
- ✅ Form Request (`PostRequest.php`) with validation rules
- ✅ Controller (`PostController.php`) with all CRUD methods
- ✅ Views (index, create, edit, show) with Bootstrap/Tailwind
- ✅ Routes (automatically registered in `routes/lara-crud.php`)

## 📖 Usage

### Basic Command

```bash
php artisan make:crud {name} --interactive
```

### Options

| Option | Description |
|--------|-------------|
| `--interactive` | **Required** - Use interactive wizard to define fields |
| `--stack=` | UI framework: `bootstrap` (default) or `tailwind` |
| `--api` | Generate API controller (skips views) |
| `--only=` | Generate only specific parts (comma-separated) |
| `--skip=` | Skip specific parts (comma-separated) |
| `--force` | Overwrite existing files |
| `--dry-run` | Preview without creating files |

### Examples

#### Generate API-only CRUD

```bash
php artisan make:crud Product --interactive --api
```

Generated: Migration, Model, Request, API Controller, Routes (no views)

#### Use Tailwind CSS

```bash
php artisan make:crud Post --interactive --stack=tailwind
```

#### Generate Specific Parts

```bash
# Only model and migration
php artisan make:crud Post --interactive --only=model,migration

# Skip views and routes
php artisan make:crud Post --interactive --skip=views,routes
```

#### Preview Before Generating

```bash
php artisan make:crud Post --interactive --dry-run
```

#### Force Overwrite Existing Files

```bash
php artisan make:crud Post --interactive --force
```

## 🎨 Field Types

The interactive wizard supports these field types:

| Type | HTML Input | Database Column | Cast Type |
|------|------------|-----------------|-----------|
| `string` | text | string(255) | - |
| `text` | textarea | text | string |
| `email` | email | string(255) | - |
| `password` | password | string(255) | - (hidden) |
| `integer` | number | integer | integer |
| `bigint` | number | bigInteger | integer |
| `decimal` | number | decimal(8,2) | decimal:2 |
| `boolean` | checkbox | boolean | boolean |
| `date` | date | date | date |
| `datetime` | datetime-local | dateTime | datetime |
| `select` | select | string(255) | - |
| `mediumtext` | textarea | mediumText | string |
| `longtext` | textarea | longText | string |

## 📝 Generated Files

### Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->boolean('is_published');
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

### Model (with auto-generated properties)

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $hidden = [
        // Auto-populated if password fields exist
    ];
}
```

### Form Request (with custom messages)

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'required|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'is_published.required' => 'The is published field is required.',
            'is_published.boolean' => 'The is published must be true or false.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'title',
            'is_published' => 'is published',
            'published_at' => 'published at',
        ];
    }
}
```

### Controller (Web)

```php
<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::paginate(15);
        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        $post = new Post();
        return view('posts.create', compact('post'));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        Post::create($request->validated());
        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());
        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
```

### API Controller

```php
<?php

namespace App\Http\Controllers;

use App\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::paginate(15);
        return response()->json($posts);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $post = Post::create($request->validated());
        return response()->json($post, 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $post->update($request->validated());
        return response()->json($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();
        return response()->json(null, 204);
    }
}
```

### Views (Bootstrap 5)

Beautiful, responsive views are generated with:
- List view with table and pagination
- Create/Edit forms with all fields
- Show view with field details
- Form validation error display
- Success/error flash messages

### Views (Tailwind CSS)

Clean, modern Tailwind views with:
- Responsive design
- Dark mode compatible structure
- Clean utility-first CSS
- Professional styling

### Routes

Routes are automatically registered in `routes/lara-crud.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

// Web routes
Route::resource('posts', \App\Http\Controllers\PostController::class);

// API routes (if --api flag used)
Route::apiResource('products', \App\Http\Controllers\ProductController::class);
```

This file is automatically included in your `web.php` or `api.php`.

## 🎯 Advanced Usage

### Select Fields with Options

When choosing `select` as field type, you'll be prompted to add options:

```
Field name: status
Type: select
Nullable? No
Validation rules: required|string

Add select options (key => label):
Option key: draft
Option label: Draft

Option key: published  
Option label: Published

Option key: archived
Option label: Archived

Option key: (blank to finish)
```

Generated dropdown:

```html
<select name="status" class="form-control">
    <option value="">-- Select Status --</option>
    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
    <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
</select>
```

### Nullable Fields

Fields marked as nullable in the wizard:
- ✅ Get `->nullable()` in migration
- ✅ Get `nullable|` in validation rules
- ✅ Properly handled in forms

### Password Fields

Password fields are handled specially:
- ✅ NOT added to `$fillable` (security)
- ✅ Added to `$hidden` array
- ✅ Use `password` input type
- ✅ No value attribute (security)

## 🔧 Customization

### Publishing Stubs

You can publish and customize the stub templates:

```bash
php artisan vendor:publish --tag=lara-crud-stubs
```

Stubs will be copied to `resources/stubs/vendor/lara-crud/` where you can modify them.

### Custom Namespaces

The package respects your Laravel configuration:
- Models: `app_path()` or custom `config/app.php` setting
- Controllers: `app/Http/Controllers`
- Requests: `app/Http/Requests`

### Custom Views Location

Views are generated in `resources/views/{plural-snake-case}/`

Example: `Post` → `resources/views/posts/`

## 🧪 Testing

The package includes comprehensive tests:

```bash
# Run all tests
composer test

# Run specific test suite
vendor/bin/phpunit tests/Unit
vendor/bin/phpunit tests/Feature

# Run with coverage
vendor/bin/phpunit --coverage-html coverage
```

## 📚 What's New in v2

### New Features
- ✨ Auto-generated model fillable, casts, and hidden properties
- ✨ Custom validation messages and attributes in form requests
- ✨ Nullable field detection and handling
- ✨ Select field with custom options support
- ✨ Tailwind CSS view stack support
- ✨ Improved Bootstrap 5 views
- ✨ Better error handling and validation

### Improvements
- 🚀 Faster generation with optimized code
- 🎨 Cleaner, more maintainable generated code
- 📝 Better documentation and examples
- 🧪 Comprehensive test coverage (90%+)
- 🐛 Bug fixes and stability improvements

### Breaking Changes from v1
- `--interactive` flag is now **required** (no more JSON config files)
- Minimum PHP version: 8.2 (was 8.1)
- Minimum Laravel version: 10.x (was 9.x)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🐛 Bug Reports

If you discover any bugs, please open an issue on [GitHub](https://github.com/shreyasarker/lara-crud/issues).

## 📝 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## 🔒 Security

If you discover any security-related issues, please email shreya@codeboid.com instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👤 Author

**Shreya Sarker**
- Email: shreya@codeboid.com
- GitHub: [@shreyasarker](https://github.com/shreyasarker)

## 🙏 Credits

- [Laravel](https://laravel.com) - The PHP Framework
- All contributors who have helped improve this package

## ⭐ Show Your Support

Give a ⭐️ if this project helped you!

---

<p align="center">Made with ❤️ by <a href="https://github.com/shreyasarker">Shreya Sarker</a></p>