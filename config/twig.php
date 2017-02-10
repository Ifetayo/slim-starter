<?php 
//read http://twig.sensiolabs.org/doc/2.x/api.html documentation for twig parse options
return [

	'twig' => [
		'views' => [
			'path' => __DIR__.'/../resources/views'
		],
		'parseOptions' => [
			'debug' => true,
	    	'charset' => 'UTF-8',
	    	'base_template_class' => 'Twig_Template',
	    	'strict_variables' => false,
	    	'autoescape' => 'html',
	    	'cache' => __DIR__.'/../storage/views/cache',
	    	'auto_reload' => null,
	    	'optimizations' => -1,
		]
		
	]
];