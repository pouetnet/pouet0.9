<?php

function cidr_match($ip, $cidr)
{
    list($subnet, $mask) = explode('/', $cidr);

    if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
    {
        return true;
    }

    return false;
}

function get_allowed_ips()
{
	/** Get the list of allowed github server to trigger deploys */
	$options = array(
		'http' => array(
			'header' => "User-Agent: pouet.net"
		)
	);

	$context = stream_context_create($options);
	$raw = file_get_contents('https://api.github.com/meta', false, $context);
	$json = json_decode($raw);

	return $json->hooks;
}

function is_remote_ip_allowed()
{
	$valid_ip = false;
	foreach (get_allowed_ips() as $ip)
	{
		if (cidr_match($_SERVER['REMOTE_ADDR'], $ip))
		{
			$valid_ip = true;
		}
	}

	return $valid_ip;
}

// Are we getting a request from github ?
if (!is_remote_ip_allowed())
{
	exit(0);
}

try
{
	// Try to get a workable structure out of the github request
	$payload = json_decode(stripslashes($_REQUEST['payload']));
}
catch(Exception $e)
{
	exit(0);
}

if ($payload->after)
{
	// Put the latest commit a text file to trigger the next deploy
	file_put_contents('../REMOTE_COMMIT', $payload->after);
}
