<?php
$c = file_get_contents('resources/views/welcome.blade.php');
$c = preg_replace('/(?<=[\s\"\'])bg-surface(?=[\s\"\'])/', 'bg-surface dark:bg-stone-900', $c);
$c = preg_replace('/(?<=[\s\"\'])text-ink(?=[\s\"\'])/', 'text-ink dark:text-stone-100', $c);
$c = preg_replace('/(?<=[\s\"\'])text-ink\/70(?=[\s\"\'])/', 'text-ink/70 dark:text-stone-300', $c);
$c = preg_replace('/(?<=[\s\"\'])bg-white(?=[\s\"\'])/', 'bg-white dark:bg-stone-800', $c);
$c = preg_replace('/(?<=[\s\"\'])bg-white\/85(?=[\s\"\'])/', 'bg-white/85 dark:bg-stone-800/85', $c);
$c = preg_replace('/(?<=[\s\"\'])border-border(?=[\s\"\'])/', 'border-border dark:border-stone-700', $c);
file_put_contents('resources/views/welcome.blade.php', $c);
echo "Done";
