<?php

const DB_MONGODB = 'norse';
const DB_REDIS = 1;

# time zone
date_default_timezone_set('Europe/Kiev');

# dev
$app['debug'] = 1;

# Cron
$app['service.cron.secretKey'] = '6as$6tdygy#b21h^8YGSAYD!8y2bh3i@*213';

# Redis
$app['redis.options'] = [];

$app['redis.channels'] = [
    'notification.queue',
];

$app['redis.parameters'] = [
    'host' => '127.0.0.1',
    'port' => '6379',
    'database' => DB_REDIS,
    'read_write_timeout' => 0,
];


# Converters
$app['converter.callbacks'] = [
    \e1\providers\Convert\Converter\Model::class
];

# SMS

$app['sms.source'] = 'SkryMed';
$app['sms.description'] = 'zabbix';
$app['sms.user'] = '380669343289';
$app['sms.password'] = 'skry666666';

# Hook
$app['hook.listeners'] = [];

# Asset
$app['assets.version'] = 'v2';
$app['assets.version_format'] = '%s?version=%s';
$app['assets.named_packages'] = [
    'asset' => ['base_path' => '/assets/'],
    'asset.bower' => ['version' => 'v1', 'base_path' => '/assets/vendor/'],
    'static.tmp' => ['version' => 'v1', 'base_path' => '/static/tmp/'],
];

# Negotiator

$app['negotiator.headers'] = [
    'Accept' => ['text/html', 'application/json', 'multipart/form-data'],
    'Content-Type' => ['multipart/form-data'],
];

# Twig
$app['twig.path'] = dirname($app['base.dir']) . '/views';
$app['twig.form.templates'] = ['bootstrap_3_layout.html.twig'];
$app['twig.options'] = ['cache' => $app['base.dir'] . '/runtime/cache/twig'];


# DB Eloquent
$app['db.config'] = [
    'driver' => 'mongodb',
    'host' => '127.0.0.1',
    'port' => '27017',
    'database' => DB_MONGODB,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
];

# Translate
$app['translator.path'] = dirname($app['base.dir']) . '/e1/lang';
$app['translator.locales'] = 'ru|en|ua';

# RBAC API
$app['api.security.authorization_checker.passwordResetTokenExpire'] = 3600;

# RBAC SITE
$app['api.security.authorization_checker.passwordResetTokenExpire'] = 3600;
$app['api.security.authorization_checker.key'] = '189476TyeGgdD';

# RBAC JWT
$app['jwt.security.key'] = 'kittyKey';
$app['jwt.security.attr.exp'] = 5184000; # 60 days

# Simple Pie
$app['simplePie.cache.location'] = $app['base.dir'] . '/runtime/cache/';
$app['simplePie.cache.life'] = 3600;
$app['simplePie.cache.disabled'] = false;
$app['simplePie.strip_html_tags.disabled'] = false;
$app['simplePie.strip_attribute.disabled'] = false;
$app['simplePie.strip_attributes.tags'] = ['bgsound', 'class', 'expr', 'id', 'style', 'onclick', 'onerror', 'onfinish', 'onmouseover', 'onmouseout', 'onfocus', 'onblur', 'lowsrc', 'dynsrc'];
$app['simplePie.strip_html_tags.tags'] = ['base', 'blink', 'body', 'doctype', 'embed', 'font', 'form', 'frame', 'frameset', 'html', 'iframe', 'input', 'marquee', 'meta', 'noscript', 'object', 'param', 'script', 'style'];
