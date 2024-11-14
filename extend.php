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
use Flarum\User\Event\Registered as UserRegistered;

use Flarum\Extend;
use Flarum\User\Event\AvatarSaving;
use Hamcq\NewPostMinitor\Listener\CheckAvatar;
use Hamcq\NewPostMinitor\Listener\CheckPost;
use Hamcq\NewPostMinitor\Listener\CheckRegister;

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
        ->listen(UserRegistered::class, CheckRegister::class)
        ->listen(AvatarSaving::class, CheckAvatar::class),
];
