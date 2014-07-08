<?php

	class JsonUtils {
		public static function getJson($url) {
		    // cache files are created like cache/abcdef123456...
		    $cacheFile = 'cache' . DIRECTORY_SEPARATOR . md5($url);

		    if (file_exists($cacheFile)) {
		        $fh = fopen($cacheFile, 'r');
		        $cacheTime = trim(fgets($fh));

		        // if data was cached recently, return cached data
		        if ($cacheTime > strtotime('-60 minutes')) {
		            return fread($fh);
		        }

		        // else delete cache file
		        fclose($fh);
		        unlink($cacheFile);
		    }

		    $json = file_get_contents($url);

		    $fh = fopen($cacheFile, 'w');
		    fwrite($fh, time() . "\n");
		    fwrite($fh, $json);
		    fclose($fh);

		    return $json;
		}


	}