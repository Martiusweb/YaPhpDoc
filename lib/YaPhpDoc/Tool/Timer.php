<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * The timer helps to perform some benchmarks about execution time and used
 * memory. 
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tool_Timer
{
	/**
	 * Does we watch memory usage ?
	 * @var bool
	 */
	protected $watch_memory;
	
	/**
	 * Microtime values when starting timer.
	 * @var array
	 */
	protected $time_start;
	
	/**
	 * Microtime values when stopping (or pausing) timer.
	 * @var array
	 */
	protected $time_end;
	
	/**
	 * Memory usage at start
	 * @var int
	 */
	protected $memory_start;
	
	/**
	 * Memory usage at end
	 * @var int
	 */
	protected $memory_end;
	
	/**
	 * Is the timer started ?
	 * @var bool
	 */
	protected $counting = false;
	
	/**
	 * Contructor of the timer.
	 * 
	 * @param bool $watch_memory Also enable memory benchmark.
	 */
	public function __construct($watch_memory = true)
	{
		$this->watch_memory = $watch_memory;
	}
	
	/**
	 * Starts the timer.
	 * @return YaPhpDoc_Tool_Timer
	 */
	public function start()
	{
		if($this->watch_memory)
			$this->memory_start = memory_get_usage();
		
		$this->time_start = array();
		$this->time_end = array();
		$this->resume();
		
		return $this;
	}
	
	/**
	 * Pauses the timer.
	 * @return YaPhpDoc_Tool_Timer
	 */
	public function pause()
	{
		if($this->counting)
		{
			array_push($this->time_end, microtime());
			$this->counting = false;
		}
		
		return $this;
	}
	
	/**
	 * Restarts the timer.
	 * @return YaPhpDoc_Tool_Timer
	 */
	public function resume()
	{
		if(!$this->counting)
		{
			$this->counting = true;
			array_push($this->time_start, microtime());
		}
		
		return $this;
	}
	
	/**
	 * Stops the timer.
	 * @return YaPhpDoc_Tool_Timer
	 */
	public function stop()
	{
		if($this->watch_memory)
			$this->memory_end = memory_get_usage();
		
		$this->pause();
		
		return $this;
	}
	
	/**
	 * Returns difference of memory usage between start() and stop().
	 * @return string
	 */
	public function getMemoryUsage()
	{
		$memory = $this->memory_end - $this->memory_start;
		$unit = 'B';
		if($memory >= 1024)
		{
			$memory /= 1024;
			$unit = 'KB';
			
			if($memory >= 1024)
			{
				$memory /= 1024;
				$unit = 'MB';
			}
		}
		
		return $memory.' '.$unit;
	}
	
	/**
	 * Returns timer counted elapsed time.
	 * @return float 
	 */
	public function getTimeUsage()
	{
		$time = 0;
		for($i = 0, $j = count($this->time_start); $i < $j; ++$i)
		{
			if(isset($this->time_end[$i]))
			{
				$tmp_start = explode(' ', $this->time_start[$i]);
				$tmp_stop  = explode(' ', $this->time_end[$i]);
				$time += ($tmp_stop[0] + $tmp_stop[1]) - ($tmp_start[0] + $tmp_start[1]);
			}
		}
		return $time;
	}
}