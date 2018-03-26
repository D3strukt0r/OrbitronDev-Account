<?php

namespace Deployer;

require 'recipe/symfony4.php';

set('bin/php', function () {
    return locateBinaryPath('/usr/local/php72/bin/php');
});

// Project name
set('application', 'account');

// Environment vars
set('env', [
    'APP_ENV' => 'prod'
]);
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader  --ignore-platform-reqs');

// Project repository
set('repository', 'https://github.com/OrbitronDev/service-account.git');
set('branch', 'master');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', ['.htaccess']);
add('shared_dirs', ['var/data']);

// Writable dirs by web server 
//add('writable_dirs', []);
set('writable_dirs', []);

// Hosts
host('hostinger')
    ->hostname('orbitrondev')
    ->set('deploy_path', '/home/u530305173/public_html/account')
    ->set('http_user', 'u530305173');
host('hostpoint')
    ->hostname('orbitrondev2')
    ->set('deploy_path', '/home/manuelev/www/account')
    ->set('http_user', 'manuelev');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
//before('deploy:symlink', 'database:migrate');
