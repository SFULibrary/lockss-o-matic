<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$dir = __DIR__;

$config = array(
//    'theme'                => 'symfony',
    'title'                => 'Circus Internal API',
    'build_dir'            => $dir . '/web/docs/api',
    'cache_dir'            => $dir . '/var/cache/sami',
    'remote_repository'    => new GitHubRemoteRepository('ubermichael/circus', $dir),
    'default_opened_level' => 2,
);

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('src');

return new Sami($iterator, $config);
