<?php
use SilverStripe\Core\Environment;
use SilverStripe\Control\Director;
/*
 * SimpleCache v1.3.0
 *
 * By Gilbert Pellegrom
 * http://dev7studios.com
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

 */



class SimpleCache {

	//Path to cache folder (with trailing /)
	var $cache_path = 'cache/';
	//Length of time to cache a file in seconds
	
	function getCacheTime(){
		if(Environment::getEnv('UI_CALENDAR_CACHE_TIME')){
			return Environment::getEnv('UI_CALENDAR_CACHE_TIME');
		} else{
			return 3600;
		}
	}


	//This is just a functionality wrapper function
	function get_data($label, $url)
	{
		if($data = $this->get_cache($label)){
			return $data;
		} else {
			$data = $this->do_curl($url);
			$this->set_cache($label, $data);
			return $data;
		}
	}

	function set_cache($label, $data)
	{
		file_put_contents(BASE_PATH .'/'. $this->cache_path . $this->safe_filename($label) .'.cache', $data);
	}

	function get_cache($label)
	{
		if($this->is_cached($label)){
            $filename = BASE_PATH .'/'. $this->cache_path . $this->safe_filename($label) .'.cache';
			return file_get_contents($filename);
		}

		return false;
	}

	function is_cached($label)
	{
		$filename = BASE_PATH .'/'. $this->cache_path . $this->safe_filename($label) .'.cache';

		if(file_exists($filename) && (filemtime($filename) + $this->getCacheTime() >= time())) return true;

		return false;
	}

	//Helper function for retrieving data from url
	function do_curl($url)
	{
		if(function_exists("curl_init")){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		} else {
			return file_get_contents($url);
		}
	}

	//Helper function to validate filenames
	function safe_filename($filename)
	{
		return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
	}
}

?>
