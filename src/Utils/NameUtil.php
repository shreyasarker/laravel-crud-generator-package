<?php

namespace ShreyaSarker\LaraCrud\Utils;

use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class NameUtil
{
    public static function getNamingConvention(string $name): array
    {
        $singular = Pluralizer::singular($name);
        $plural = Pluralizer::plural($name);

        return [
            // Class names
            'singular_upper' => Str::studly($singular),   // Post, BlogPost
            'plural_upper'   => Str::studly($plural),     // Posts, BlogPosts

            // Variables / folders
            'singular_lower' => Str::camel($singular),    // post, blogPost
            'plural_lower'   => Str::camel($plural),      // posts, blogPosts

            // Database / routes
            'table_name'     => Str::snake($plural),      // blog_posts

            // UI labels
            'label_upper'    => Str::headline($singular), // Blog Post
            'label_lower'    => Str::lower(Str::headline($singular)), // blog post
        ];
    }
}
