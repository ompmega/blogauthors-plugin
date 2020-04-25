<?php namespace Ompmega\BlogAuthors;

use Backend;
use Event;
use System\Classes\PluginBase;

/**
 * BlogAuthors Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Blog'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Blog Authors',
            'description' => 'Show post author profiles in the front end.',
            'author'      => 'Ompmega',
            'icon'        => 'icon-user-circle-o'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \RainLab\Blog\Models\Post::extend(function ($model) {
            /** @var \RainLab\Blog\Models\Post $model */
            $model->belongsTo['user'] = [
                'Ompmega\BlogAuthors\Models\Author',
                'table' => 'ompmega_blogauthors_posts_authors'
            ];
        });

        // Extend the navigation
        Event::listen('backend.menu.extendItems', function ($manager) {
            /** @var \Backend\Classes\NavigationManager $manager */
            $manager->addSideMenuItems('RainLab.Blog', 'blog', [
                'authors' => [
                    'label' => 'Authors',
                    'icon' => 'icon-user-circle-o',
                    'code' => 'authors',
                    'owner' => 'RainLab.Blog',
                    'url' => Backend::url('ompmega/blogauthors/authors'),
                ]
            ]);
        });

        Event::listen('backend.form.extendFields', function ($form) {
            /** @var \Backend\Widgets\Form $form */

            if (!$form->getController() instanceof \RainLab\Blog\Controllers\Posts) {
                return;
            }

            if (!$form->model instanceof \RainLab\Blog\Models\Post) {
                return;
            }

            $form->addSecondaryTabFields([
                'user' => [
                    'label' => 'Author',
                    'type' => 'relation',
                    'commentAbove' => 'Select the author for this blog post.',
                    'span' => 'right',
                    'tab' => 'rainlab.blog::lang.post.tab_manage'
                ]
            ]);

        });

        \RainLab\Blog\Components\Posts::extend(function ($component) {
            /** @var \RainLab\Blog\Components\Posts $component */
            $component->setProperty('author_slug', [
                'title' => 'Author slug',
                'description' => 'Look up an author using the supplied slug value.',
                'default' => '{{ :slug }}',
                'type' => 'string'
            ]);
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Ompmega\BlogAuthors\Components\Author' => 'postAuthor',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'ompmega.blogauthors.some_permission' => [
                'tab' => 'BlogAuthors',
                'label' => 'Some permission'
            ],
        ];
    }
}
