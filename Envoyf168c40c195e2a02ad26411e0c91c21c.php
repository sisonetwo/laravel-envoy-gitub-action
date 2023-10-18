<?php $storage = isset($storage) ? $storage : null; ?>
<?php $env = isset($env) ? $env : null; ?>
<?php $serve = isset($serve) ? $serve : null; ?>
<?php $deployment = isset($deployment) ? $deployment : null; ?>
<?php $builds = isset($builds) ? $builds : null; ?>
<?php $date = isset($date) ? $date : null; ?>
<?php $branch = isset($branch) ? $branch : null; ?>
<?php $appDir = isset($appDir) ? $appDir : null; ?>
<?php $repo = isset($repo) ? $repo : null; ?>

<?php $__container->servers(['production' => ['vps@103.187.147.41']]); ?>
 
<?php
    $repo = git@github.com:sisonetwo/laravel-envoy-gitub-action.git;
    $appDir = '/var/www';
    $branch = 'master';

    date_default_timezone_set('Asia/Jakarta');
    $date = date('YmdHis');

    $builds = $appDir . '/sources';
    $deployment = $builds . '/' . $date;

    $serve = $appDir . '/source';
    $env = $appDir . '/.env';
    $storage = $appDir . '/storage';
?>

<?php $__container->startMacro('deploy'); ?>
    git
    install
    live
<?php $__container->endMacro(); ?>

<?php $__container->startTask('git', ['on' => 'production']); ?>
    git clone -b <?php echo $branch; ?> "<?php echo $repo; ?>" <?php echo $deployment; ?>

<?php $__container->endTask(); ?>

<?php $__container->startTask('install', ['on' => 'production']); ?>
    cd <?php echo $deployment; ?>


    rm -rf <?php echo $deployment; ?>/storage
    
    ln -nfs <?php echo $env; ?> <?php echo $deployment; ?>/.env
    
    ln -nfs <?php echo $storage; ?> <?php echo $deployment; ?>/storage

    composer install --prefer-dist --no-dev
    
    php ./artisan migrate --force
<?php $__container->endTask(); ?>

<?php $__container->startTask('live', ['on' => 'production']); ?>
    cd <?php echo $deployment; ?>

    
    ln -nfs <?php echo $deployment; ?> <?php echo $serve; ?>


    chown -R www-data: /var/www
 
    chown -R www-data: /var/www/sources/

    systemctl restart php8.0-fpm

    systemctl restart nginx
<?php $__container->endTask(); ?>
