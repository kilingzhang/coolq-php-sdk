<?php

namespace Kilingzhang\QQ;


function http_server(string $key, $default = '')
{
    return empty($_SERVER[$key]) ? $default : $_SERVER[$key];
}

function http_put(): array
{
    $content = file_get_contents('php://input');
    $content = json_decode($content, true);
    return empty($content) ? [] : $content;
}