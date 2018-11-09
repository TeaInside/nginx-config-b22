<?php

chdir(__DIR__);

/**
 * @param string $pDir
 * @param callable $callback
 * @param array $exceptions
 * @return void
 */
function rscanr_callback(string $pDir = ".", callable $callback, array $exceptions = []): void
{
	foreach ($exceptions as &$exception) {
		$exception = realpath($exception);
	}
	unset($exception);

	$s = scandir($pDir);
	unset($s[0], $s[1]);
	foreach ($s as $v) {
		$v = realpath($pDir."/".$v);

		if (!in_array($v, $exceptions)) {
			$callback($v);
			if (is_dir($v)) {
				rscanr_callback($v, $callback);
			}
		}
	}
}

rscanr_callback(__DIR__."/sites-available", function (string $f) {
	if (!is_dir($f)) {
		print shell_exec("ln -svf {$f} /etc/nginx/sites-enabled 2>&1");
	}
});

print shell_exec("/etc/init.d/nginx restart 2>&1");
