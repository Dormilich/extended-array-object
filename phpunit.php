<?php
require __DIR__ . '/vendor/autoload.php';

// set the default timezone
date_default_timezone_set('UTC');


function test_filter() 
{ 
	return true; 
}

function test_map($value) 
{ 
	return $value; 
}

function test_reduce($carry, $value)
{
	return $carry;
}

function length_compare_func($a, $b)
{
	$la = strlen((string) $a);
	$lb = strlen((string) $b);

	if ($la === $lb) {
		return 0;
	} elseif ($la > $lb) {
		return 1;
	} else {
		return -1;
	}
}

class CallbackTestMethods
{
	public function filter()
	{
		return true;
	}

	public static function static_filter()
	{
		return true;
	}

	public function map($value)
	{
		return $value;
	}

	public static function static_map($value)
	{
		return $value;
	}

	public function reduce($carry, $value)
	{
		return $carry;
	}

	public static function static_reduce($carry, $value)
	{
		return $carry;
	}

	public function length_compare($a, $b)
	{
		return length_compare_func($a, $b);
	}

	public static function static_length_compare($a, $b)
	{
		return length_compare_func($a, $b);
	}
}
