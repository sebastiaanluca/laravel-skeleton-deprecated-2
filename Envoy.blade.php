@include(__DIR__ . '/vendor/autoload.php')

@setup
(new \Dotenv\Dotenv(__DIR__, '.env'))->load();

/* Project info */

$projectName = getenv('DEPLOY_PROJECT_NAME');
$repository = getenv('DEPLOY_REPOSITORY');
$branch = getenv('DEPLOY_BRANCH');

/* Remote server */

$server = getenv('DEPLOY_SERVER');
$port = getenv('DEPLOY_SERVER_PORT');
$user = getenv('DEPLOY_USER');

$baseDirectory = "/var/www/{$projectName}";
$releasesDirectory = "{$baseDirectory}/releases";
$storageDirectory = "{$baseDirectory}/storage";
$currentDirectory = "{$releasesDirectory}/current";
$newReleaseName = date('Ymd-His');
$newReleaseDirectory = "{$releasesDirectory}/{$newReleaseName}";
$keepReleases = getenv('DEPLOY_KEEP_RELEASES');

$deployLogDir = "$baseDirectory/deploy-logs";
$log = "$deployLogDir/deploy-$newReleaseName.log";

/* Helpers */

function logMessage($message, $icon = '', $color = null) {
    if(! empty($icon)) {
        $icon = $icon . '  ';
    }
    
    if($color) {
        $message = "\033[3{$color}m{$message}\033[0m";
    }

    return "echo '{$icon}' '$message'";
}

function logTaskStart($message) {
    return logMessage($message, "\u{2728}");
}

function logCompletedTask($message) {
    return logMessage($message, "\u{2705}", 2);
}
@endsetup

{{-- -A enables SSH agent forwarding which uses local keys instead of those on the server --}}
@servers(['local' => '127.0.0.1', 'remote' => "{$server} -p {$port} -l {$user} -A"])

{{-- Stories --}}

@story('deploy')
initialize remote
update permissions
initialize log file
verify configuration
back up current release
clone
configure
set release permissions
symlink storage directories
install composer packages
optimize project
install npm modules
build front-end assets
migrate database
activate release
clear cache
cache config
restart queue
clean up old releases
display completed message
@endstory

{{-- Tasks --}}

@task('initialize remote', ['on' => 'remote'])
{{ logTaskStart('Preparing the remote environment…') }}

mkdir -p {{ $baseDirectory }}
mkdir -p {{ $releasesDirectory }}
mkdir -p {{ $deployLogDir }}

mkdir -p {{ $storageDirectory }}
mkdir -p {{ $storageDirectory }}/app
mkdir -p {{ $storageDirectory }}/app/uploads
mkdir -p {{ $storageDirectory }}/framework
mkdir -p {{ $storageDirectory }}/framework/views
mkdir -p {{ $storageDirectory }}/logs

{{ logCompletedTask('Remote server environment ready') }}
@endtask

@task('update permissions', ['on' => 'remote'])
{{ logTaskStart('Updating directories and files ownership and permissions…') }}

find {{ $baseDirectory }} -maxdepth 1 -exec sudo chown deploy:www-data {} \;
find {{ $baseDirectory }} -type d -maxdepth 1 -exec sudo chmod 2775 {} \;
find {{ $baseDirectory }} -type f -maxdepth 1 -exec sudo chmod 664 {} \;

find {{ $releasesDirectory }} -maxdepth 1 -exec sudo chown deploy:www-data {} \;
find {{ $releasesDirectory }} -type d -maxdepth 1 -exec sudo chmod 2775 {} \;
find {{ $releasesDirectory }} -type f -maxdepth 1 -exec sudo chmod 664 {} \;

find {{ $storageDirectory }} -exec sudo chown deploy:www-data {} \;
find {{ $storageDirectory }} -type d -exec sudo chmod 2777 {} \;
find {{ $storageDirectory }} -type f -exec sudo chmod 666 {} \;

{{ logCompletedTask('Permissions set') }}
@endtask

@task('initialize log file', ['on' => 'remote'])
{{ logTaskStart('Initializing log file…') }}
touch {{ $storageDirectory }}/logs/laravel.log
sudo chown www-data:www-data {{ $storageDirectory }}/logs/laravel.log
sudo chmod 666 {{ $storageDirectory }}/logs/laravel.log
{{ logCompletedTask('Log file initialized') }}
@endtask

@task('verify configuration', ['on' => 'remote'])
{{ logTaskStart('Verifying .env configuration file…') }}

cd {{ $baseDirectory }}

if [ ! -f .env ]; then \
    {{ logMessage('No configuration file found!', "\u{274C}", 1) }}; \
    {{ logMessage('Create the .env file manually in the root directory before deploying again', "\u{1F4D8}", 4) }}; \
exit 1; fi

{{ logCompletedTask('Configuration file found') }}
@endtask

@task('clone', ['on' => 'remote'])
{{ logTaskStart('Cloning the project…') }}
{{-- Prevent git clone failure when connecting for the first time as it asks to verify the host and a passphrase --}}
GIT_SSH_COMMAND="ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no" git clone --depth 1 -b {{ $branch }} "{{ $repository }}" {{ $newReleaseDirectory }} >>{{ $log }} 2>&1
{{ logCompletedTask('Project cloned') }}
@endtask

@task('configure', ['on' => 'remote'])
{{ logTaskStart('Symlinking .env configuration file…') }}
cd {{ $baseDirectory }}
ln -nfs {{ $baseDirectory }}/.env {{ $newReleaseDirectory }}/.env
{{ logCompletedTask('Project configured') }}
@endtask

@task('set release permissions', ['on' => 'remote'])
{{ logTaskStart('Setting default directory and file permissions…') }}
find {{ $newReleaseDirectory }} -exec sudo chown deploy:www-data {} \;
find {{ $newReleaseDirectory }} -type d -exec sudo chmod 2775 {} \;
find {{ $newReleaseDirectory }} -type f -exec sudo chmod 664 {} \;
{{ logCompletedTask('Default permissions set') }}
@endtask

@task('symlink storage directories', ['on' => 'remote'])
{{ logTaskStart('Symlinking storage directories…') }}
rm -rf {{ $newReleaseDirectory }}/storage
ln -nfs {{ $storageDirectory }} {{ $newReleaseDirectory }}/storage

rm -rf {{ $newReleaseDirectory }}/public/uploads
ln -nfs {{ $storageDirectory }}/app/uploads {{ $newReleaseDirectory }}/public/uploads
{{ logCompletedTask('Storage directory configured') }}
@endtask

@task('install composer packages', ['on' => 'remote'])
{{ logTaskStart('Installing composer packages…') }}
cd {{ $newReleaseDirectory }}
composer install --prefer-dist --no-scripts --no-dev -q -o --no-interaction --no-progress >>{{ $log }} 2>&1
{{ logCompletedTask('Composer packages installed') }}
@endtask

@task('optimize project', ['on' => 'remote'])
{{ logTaskStart('Optimizing project code…') }}
cd {{ $newReleaseDirectory }}
php artisan clear-compiled >>{{ $log }} 2>&1
composer dumpautoload -o >>{{ $log }} 2>&1
php artisan optimize >>{{ $log }} 2>&1
{{ logCompletedTask('Project optimized') }}
@endtask

@task('install npm modules', ['on' => 'remote'])
{{ logTaskStart('Installing npm modules…') }}
cd {{ $newReleaseDirectory }}
yarn install --force --ignore-engines >>{{ $log }} 2>&1
{{ logCompletedTask('Npm modules installed') }}
@endtask

@task('build front-end assets', ['on' => 'remote'])
{{ logTaskStart('Generating assets…') }}
cd {{ $newReleaseDirectory }}
yarn run build >>{{ $log }} 2>&1
{{ logCompletedTask('Assets generated') }}
@endtask

@task('migrate database', ['on' => 'remote'])
{{ logTaskStart('Migrating database…') }}
cd {{ $newReleaseDirectory }}
php artisan migrate --force >>{{ $log }} 2>&1
{{ logCompletedTask('Database migrated') }}
@endtask

@task('activate release', ['on' => 'remote'])
{{ logTaskStart('Activating the new release…') }}
ln -nfs {{ $newReleaseDirectory }} {{ $currentDirectory }}
{{ logCompletedTask('New release activated') }}
@endtask

@task('clear cache', ['on' => 'remote'])
{{ logTaskStart('Clearing cache…') }}
cd {{ $currentDirectory }}

php artisan cache:clear >>{{ $log }} 2>&1
php artisan view:clear >>{{ $log }} 2>&1

# Clear opcache, etc.
#sudo service php7.1-fpm reload >>{{ $log }} 2>&1
#sudo service nginx reload >>{{ $log }} 2>&1
{{ logCompletedTask('Cache cleared') }}
@endtask

@task('cache config', ['on' => 'remote'])
{{ logTaskStart('Cache configuration and routes…') }}
cd {{ $currentDirectory }}

php artisan config:cache >>{{ $log }} 2>&1
php artisan route:cache >>{{ $log }} 2>&1
{{ logCompletedTask('Configuration and routes cached') }}
@endtask

@task('restart queue', ['on' => 'remote'])
{{ logTaskStart('Restarting the queue…') }}
cd {{ $currentDirectory }}
php artisan queue:restart >>{{ $log }} 2>&1
{{ logCompletedTask('Queue restarted') }}
@endtask

@task('clean up old releases', ['on' => 'remote'])
{{ logTaskStart('Purging old releases…') }}
cd {{ $releasesDirectory }}
ls -dt {{ $releasesDirectory }}/* | grep -v '\current$' | tail -n +{{ $keepReleases + 1 }} | xargs -d "\n" rm -rf
{{ logCompletedTask('Old releases purged') }}
@endtask

@task('display completed message', ['on' => 'remote'])
{{ logMessage('Project successfully deployed!', "\u{1F37B}", 2) }}
@endtask

{{-- // --}}

@task('back up current release', ['on' => 'remote'])
{{ logTaskStart('Backing up current release…') }}

if [ -d "{{ $currentDirectory }}" ]; then
    cd {{ $currentDirectory }}

    if php artisan backup:run 3>&1 1>>{{ $log }} 2>&1;
    then
        {{ logCompletedTask('Current release backed up') }}
    else
        {{ logMessage('Could not back up current release', "\u{2757}", 3) }}
    fi
else
    {{ logMessage('No current release to back up', "\u{2757}", 3) }}
fi
@endtask

@task('seed current release', ['on' => 'remote'])
{{ logTaskStart('Seeding database…') }}
cd {{ $currentDirectory }}
php artisan db:seed --class=ProductionSeeder --force >>{{ $log }} 2>&1
{{ logCompletedTask('Database seeded') }}
@endtask
