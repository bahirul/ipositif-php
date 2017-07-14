<?php
/**
 * Create App Route [$method,$route,[$controller,$action]]
 */
return [
	['GET','/',['Default','index']],
	['GET','/kominfo/blacklist',['Kominfo','blacklist']],
	['GET','/kominfo/whitelist',['Kominfo','whitelist']],
];