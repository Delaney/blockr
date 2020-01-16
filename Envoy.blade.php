@servers(['web' => 'deployer@looka.live'])

@task('list', ['on' => 'web'])
    ls -l
@endtask

@setup
    $repository = 'git@gitlab.com:DieBrise/blockr.git';
    $releases_dir = '/var/www/blockr.looka.live/releases';
    $app_dir = '/var/www/blockr.looka.live';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
@endstory