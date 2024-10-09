<?php

use App\Mixins\ArrMixin;
use App\Mixins\FilesystemMixin;
use App\Mixins\StrMixin;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

Arr::mixin(new ArrMixin());
Filesystem::mixin(new FilesystemMixin());
Str::mixin(new StrMixin());
