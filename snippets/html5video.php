<?php if ($file = $attrs->id()->toFile()): ?>
<?php
$files = $page->files()->filterBy('name', $file->name())->filterBy('type', 'video')->shuffle();
$autoplay = $attrs->autoplay()->toBool();
$loop = $attrs->loop()->toBool();
$muted = $attrs->muted()->toBool();
$controls = $attrs->controls()->toBool();
$playsinline = $attrs->playsinline()->toBool();
?>
<figure class="<?= $attrs->css() ?>">
<video style="display: block; width: 100%"
    <?php if($autoplay):?> autoplay<?php endif ?>
    <?php if($loop):?> loop<?php endif ?>
    <?php if($muted):?> muted<?php endif ?>
    <?php if($playsinline):?> playsinline<?php endif ?>
    <?php if($controls):?> controls<?php endif ?>
>
<?php foreach ($files as $file): ?>
    <source src="<?= $file->url(); ?>" type="video/mp4">
<?php endforeach ?>
</video>
<figcaption><?= $attrs->caption() ?></figcaption>
</figure>

<?php endif ?>
