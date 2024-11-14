<?php

/*
 * This file is part of hamcq/newpostmonitor.
 *
 * Copyright (c) 2024 emin.lin.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Hamcq\NewPostMinitor;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\User\Event\Saving as UserSaving;


use Flarum\Extend;
use Hamcq\NewPostMinitor\Listener\CheckPost;
use Hamcq\NewPostMinitor\Listener\CheckUser;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),
   
    (new Extend\Event())
        ->listen(PostSaving::class, CheckPost::class)
        ->listen(UserSaving::class, CheckUser::class),
];
