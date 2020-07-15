<?php if ($file = $attrs->id()->toFile()): ?>
<?php
$files = $file->siblings()->filterBy('name', $file->name())->filterBy('type', 'video');
$autoplay = $attrs->autoplay()->toBool();
$loop = $attrs->loop()->toBool();
$muted = $attrs->muted()->toBool();
$controls = $attrs->controls()->toBool();
$playsinline = $attrs->playsinline()->toBool();
$srcattr = $attrs->lazyloading()->toBool() ? 'data-src' : 'src';
$lazy = $attrs->lazyloading()->toBool() ? 'is-lazy' : '';
?>

<figure class="<?= $attrs->css() ?>">
<video class="<?= $lazy ?>" style="display: block; width: 100%"
    <?php if($autoplay):?> autoplay<?php endif ?>
    <?php if($loop):?> loop<?php endif ?>
    <?php if($muted):?> muted<?php endif ?>
    <?php if($playsinline):?> playsinline<?php endif ?>
    <?php if($controls):?> controls<?php endif ?>
>
<?php foreach ($files as $file): ?>
    <source <?= $srcattr ?>="<?= $file->url(); ?>" type="video/<?= $file->extension(); ?>">
<?php endforeach ?>
</video>
<figcaption><?= $attrs->caption() ?></figcaption>
</figure>

<?php endif ?>
