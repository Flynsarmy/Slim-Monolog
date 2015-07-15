<?php
/**
 * Monolog Log File Writer
 *
 * Use this custom log writer to output log messages
 * to monolog.
 *
 * USAGE
 *
 * $app = new \Slim\Slim(array(
 *	 'log.writer' => new \Flynsarmy\SlimMonolog\Log\MonologWriter(array(
 *	 		'name' => 'SlimMonoLogger',
 *	        'handlers' => array(
 *	        	new \Monolog\Handler\StreamHandler('./logs/'.date('Y-m-d').'.log'),
 *	        ),
 *	        'processors' => array(
 *	        	function ($record) {
 *				    $record['extra']['dummy'] = 'Hello world!';
 *
 *				    return $record;
 *				},
 *	        ),
 *	    ))
 * ));
 *
 * SETTINGS
 *
 * You may customize this log writer by passing an array of
 * settings into the class constructor. Available options
 * are shown above the constructor method below.
 *
 * @author Flyn San <flynsarmy@gmail.com>
 * @copyright 2013 Flynsarmy
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Flynsarmy\SlimMonolog\Log;

class MonologWriter
{
	/**
	 * @var resource
	 */
	protected $resource;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Converts Slim log level to Monolog log level
	 * @var array
	 */
	protected $log_level = array(
		\Slim\Log::EMERGENCY => \Monolog\Logger::EMERGENCY,
		\Slim\Log::ALERT => \Monolog\Logger::ALERT,
		\Slim\Log::CRITICAL => \Monolog\Logger::CRITICAL,
		\Slim\Log::ERROR => \Monolog\Logger::ERROR,
		\Slim\Log::WARN => \Monolog\Logger::WARNING,
		\Slim\Log::NOTICE => \Monolog\Logger::NOTICE,
		\Slim\Log::INFO => \Monolog\Logger::INFO,
		\Slim\Log::DEBUG => \Monolog\Logger::DEBUG,
	);

	/**
	 * Constructor
	 *
	 * Prepare this log writer. Available settings are:
	 *
	 * name:
	 * (string) The name for this Monolog logger
	 *
	 * handlers:
	 * (array) Array of initialized monolog handlers - eg StreamHandler
	 *
	 * processors:
	 * (array) Array of monolog processors - anonymous functions
	 *
	 * @param   array $settings
	 * @param bool $merge
	 * @return  void
	 */
	public function __construct($settings = array(), $merge = true)
	{
		//Merge user settings
	        if ($merge) {
	            $this->settings = array_merge(array(
	                'name' => 'SlimMonoLogger',
	                'handlers' => array(
	                    new \Monolog\Handler\StreamHandler('./logs/'.date('y-m-d').'.log'),
	                ),
	                'processors' => array(),
	            ), $settings);
	        } else {
	            $this->settings = $settings;
	        }
	}

	/**
	 * Write to log
	 *
	 * @param   mixed $object
	 * @param   int   $level
	 * @return  void
	 */
	public function write($object, $level)
	{
		if ( !$this->resource )
		{
			// create a log channel
			$this->resource = new \Monolog\Logger($this->settings['name']);
			foreach ( $this->settings['handlers'] as $handler )
				$this->resource->pushHandler($handler);
			foreach ( $this->settings['processors'] as $processor )
				$this->resource->pushProcessor($processor);
		}

		// Don't bother typesetting $object, Monolog will do this for us
		$this->resource->addRecord(
			$this->get_log_level($level, \Monolog\Logger::WARNING),
			$object
		);
	}

	/**
	 * Converts Slim log level to Monolog log level
	 *
	 * @param  int $slim_log_level   Slim log level we're converting from
	 * @param  int $default_level    Monolog log level to use if $slim_log_level not found
	 * @return int                   Monolog log level
	 */
	protected function get_log_level( $slim_log_level, $default_monolog_log_level )
	{
		return isset($this->log_level[$slim_log_level]) ?
			$this->log_level[$slim_log_level] :
			$default_monolog_log_level;
	}
}
