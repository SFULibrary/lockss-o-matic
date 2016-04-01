<?php

require 'vendor/autoload.php';

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in(__DIR__ . '/src');

return new Sami($iterator, array(
    'title' => 'LOCKSSOMatic API',
    'build_dir' => __DIR__ . '/docs/api',
    'cache_dir' => __DIR__ . '/app/cache/sami',
    'remote_repository' => new GitHubRemoteRepository('ubermichael/lockss-o-matic', __DIR__),    
));
