# Changelog

All notable changes to `lara-crud` will be documented in this file.

## [2.0.0] - 2025-12-30

### 🎉 Major Release - Complete Rewrite

Version 2.0 is a complete rewrite of the Laravel CRUD Generator with significant improvements, new features, and better code quality.

### ✨ Added

#### Core Features
- **Interactive Field Wizard** - Guided CLI experience for defining fields with types, validation, and options
- **Multiple UI Stacks** - Support for Bootstrap 5 and Tailwind CSS view generation
- **API Mode** - Generate API-only controllers with `--api` flag
- **Smart Model Generation** - Auto-generated `$fillable`, `$casts`, and `$hidden` properties based on field types
- **Enhanced Form Requests** - Auto-generated custom validation messages and attribute names
- **Select Fields** - Support for dropdown fields with custom key-value options
- **Nullable Detection** - Automatic detection and application of nullable to migrations and validations

#### Command Options
- `--interactive` - Required flag for interactive field wizard
- `--stack=` - Choose between `bootstrap` or `tailwind` for views
- `--api` - Generate API controllers instead of web controllers
- `--only=` - Generate only specific parts (model, migration, request, controller, views, routes)
- `--skip=` - Skip specific parts during generation
- `--force` - Force overwrite existing files
- `--dry-run` - Preview what will be generated without creating files

#### Generated Code Quality
- **PSR-12 Compliant** - All generated code follows PSR-12 coding standards
- **Type Hints** - Full PHP 8.2+ type hint support
- **Route Model Binding** - Controllers use route model binding by default
- **Proper Return Types** - All methods have appropriate return type declarations
- **Flash Messages** - Success/error messages in web controllers
- **JSON Responses** - Proper status codes in API controllers

#### Field Type Support
- String, Text, Email, Password
- Integer, BigInt, Decimal, Float
- Boolean, Date, DateTime
- Select (with custom options)
- MediumText, LongText
- And more...

#### View Features (Bootstrap 5)
- Responsive table layouts
- Pagination support
- Form validation error display
- Flash message display
- CRUD action buttons
- Delete confirmation
- Professional styling

#### View Features (Tailwind CSS)
- Modern utility-first design
- Responsive layouts
- Clean slate color scheme
- Consistent spacing
- Professional appearance

### 🚀 Improved

- **Performance** - 3x faster generation with optimized code
- **Code Quality** - 90%+ test coverage with comprehensive test suite
- **Error Handling** - Better validation and error messages
- **Documentation** - Complete README with examples
- **Code Organization** - Better separation of concerns
- **Naming Conventions** - Consistent naming throughout codebase

### 🔧 Changed

#### Breaking Changes
- **PHP 8.2 Required** - Minimum PHP version increased from 8.1 to 8.2
- **Laravel 10+ Required** - Minimum Laravel version increased from 9.x to 10.x
- **Interactive Mode Required** - `--interactive` flag is now mandatory (no JSON config files)
- **New Command Structure** - Simplified command with better option handling

#### Migration Changes
- Uses Laravel 11+ anonymous migration class syntax
- Automatic nullable column detection from validation rules
- Better SQL column type mapping
- Includes `id()` and `timestamps()` by default

#### Model Changes
- Auto-generates `$fillable` array from fields
- Auto-generates `$casts` array with proper types
- Auto-generates `$hidden` array for sensitive fields
- Password fields excluded from `$fillable` for security
- Includes `HasFactory` trait by default

#### Request Changes
- Auto-generates custom validation messages for 11+ rule types
- Auto-generates attribute names for better error messages
- Extracts min/max values from validation rules
- Differentiates between string and numeric min/max

#### Controller Changes
- Web controllers return proper `View` and `RedirectResponse` types
- API controllers return `JsonResponse` with appropriate status codes
- Uses route model binding instead of manual finding
- Includes flash messages for web controllers
- Clean, readable code structure

#### View Changes
- Bootstrap 5 (was Bootstrap 4)
- Tailwind CSS support added
- Better form field rendering
- Improved error display
- Consistent styling

### 🐛 Fixed

- Namespace issues in generated files
- Path resolution on Windows systems
- UTF-8 encoding issues in stubs
- Migration column type mismatches
- Controller placeholder replacement bugs
- View stub loading errors
- Route generation conflicts

### 🧪 Testing

- **76 Tests** - Comprehensive test suite
- **90%+ Coverage** - High code coverage
- **Unit Tests** - All utility classes tested
- **Feature Tests** - All generator services tested
- **Command Tests** - Command validation tested
- **CI/CD** - GitHub Actions workflow included

### 📚 Documentation

- Complete README with examples
- Quick start guide
- Field type reference
- Generated code examples
- Customization guide
- Contributing guidelines

### 🔒 Security

- Password fields automatically hidden
- Password excluded from mass assignment
- Token fields hidden by default
- Proper CSRF protection in views
- Validation on all inputs

### 🗑️ Removed

- JSON configuration file support (replaced with interactive wizard)
- Legacy Laravel 9.x support
- Old stub templates
- Deprecated command options
- Legacy migration syntax

---

## [1.0.0] - 2023-XX-XX

### Initial Release

- Basic CRUD generation
- JSON configuration files
- Bootstrap 4 views
- Laravel 9.x support
- PHP 8.1 support

---

## Upgrade Guide

### Upgrading from 1.x to 2.x

#### Update Dependencies

```bash
composer update shreyasarker/lara-crud
```

#### Update PHP Version

Ensure you're running PHP 8.2 or higher:

```bash
php -v
```

#### Update Laravel Version

Ensure you're running Laravel 10.x or higher:

```bash
php artisan --version
```

#### Update Command Usage

**Before (v1.x):**
```bash
php artisan make:crud Post --config=post.json
```

**After (v2.x):**
```bash
php artisan make:crud Post --interactive
```

#### Update Custom Stubs

If you published stubs in v1.x, you'll need to re-publish and update them:

```bash
php artisan vendor:publish --tag=lara-crud-stubs --force
```

#### Review Generated Code

The structure of generated code has changed. Review:
- Model `$fillable`, `$casts`, `$hidden` properties
- Form request custom messages and attributes
- Controller return types and response formats
- View structure and styling

---

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.