
@servers(['production' => ['vps@103.187.147.41']])
 
@setup
    $repo = 'https://github.com/sisonetwo/laravel-envoy-github-action.git';
    $appDir = '/var/www';
    $branch = 'master';

    date_default_timezone_set('Asia/Jakarta');
    $date = date('YmdHis');

    $builds = $appDir . '/sources';
    $deployment = $builds . '/' . $date;

    $serve = $appDir . '/source';
    $env = $appDir . '/.env';
    $storage = $appDir . '/storage';
@endsetup

@story('deploy')
    git
    install
    live
@endstory

@task('git', ['on' => 'production'])
    git clone -b {{ $branch }} "{{ $repo }}" {{ $deployment }}
@endtask

@task('install', ['on' => 'production'])
    cd {{ $deployment }}

    rm -rf {{ $deployment }}/storage
    
    ln -nfs {{ $env }} {{ $deployment }}/.env
    
    ln -nfs {{ $storage }} {{ $deployment }}/storage

    composer install --prefer-dist --no-dev
    
    php ./artisan migrate --force
@endtask

@task('live', ['on' => 'production'])
    cd {{ $deployment }}
    
    ln -nfs {{ $deployment }} {{ $serve }}

    chown -R www-data: /var/www

    systemctl restart php8.0-fpm

    systemctl restart nginx
@endtask
