<?php declare(strict_types = 1);

// osfsl-C:/Users/resotuqu/Herd/hackatonshik/vendor/composer/../laravel/framework/src/Illuminate/Notifications/ChannelManager.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Notifications\ChannelManager
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b66e463cc5416a10057b99953802e08607934b69bcaa32e0a9bec86ec9e1a760-8.5.5-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Notifications\\ChannelManager',
        'filename' => 'C:/Users/resotuqu/Herd/hackatonshik/vendor/composer/../laravel/framework/src/Illuminate/Notifications/ChannelManager.php',
      ),
    ),
    'namespace' => 'Illuminate\\Notifications',
    'name' => 'Illuminate\\Notifications\\ChannelManager',
    'shortName' => 'ChannelManager',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 180,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Support\\Manager',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Notifications\\Dispatcher',
      1 => 'Illuminate\\Contracts\\Notifications\\Factory',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'notificationSender' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'name' => 'notificationSender',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The resolved notification sender instance.
 *
 * @var \\Illuminate\\Notifications\\NotificationSender|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'defaultChannel' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'name' => 'defaultChannel',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'mail\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 91,
            'startFilePos' => 784,
            'endTokenPos' => 91,
            'endFilePos' => 789,
          ),
        ),
        'docComment' => '/**
 * The default channel used to deliver messages.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'locale' => 
      array (
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'name' => 'locale',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The locale used when sending notifications.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 22,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'send' => 
      array (
        'name' => 'send',
        'parameters' => 
        array (
          'notifiables' => 
          array (
            'name' => 'notifiables',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 26,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'notification' => 
          array (
            'name' => 'notification',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 40,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Send the given notification to the given notifiable entities.
 *
 * @param  \\Illuminate\\Support\\Collection|mixed  $notifiables
 * @param  mixed  $notification
 * @return void
 */',
        'startLine' => 45,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'sendNow' => 
      array (
        'name' => 'sendNow',
        'parameters' => 
        array (
          'notifiables' => 
          array (
            'name' => 'notifiables',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 29,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'notification' => 
          array (
            'name' => 'notification',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'channels' => 
          array (
            'name' => 'channels',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 58,
                'endLine' => 58,
                'startTokenPos' => 155,
                'startFilePos' => 1581,
                'endTokenPos' => 155,
                'endFilePos' => 1584,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 58,
            'endColumn' => 80,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Send the given notification immediately.
 *
 * @param  \\Illuminate\\Support\\Collection|mixed  $notifiables
 * @param  mixed  $notification
 * @param  array|null  $channels
 * @return void
 */',
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'channel' => 
      array (
        'name' => 'channel',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 69,
                'endLine' => 69,
                'startTokenPos' => 192,
                'startFilePos' => 1838,
                'endTokenPos' => 192,
                'endFilePos' => 1841,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 29,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a channel instance.
 *
 * @param  string|null  $name
 * @return mixed
 */',
        'startLine' => 69,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'createDatabaseDriver' => 
      array (
        'name' => 'createDatabaseDriver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create an instance of the database driver.
 *
 * @return \\Illuminate\\Notifications\\Channels\\DatabaseChannel
 */',
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'createBroadcastDriver' => 
      array (
        'name' => 'createBroadcastDriver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create an instance of the broadcast driver.
 *
 * @return \\Illuminate\\Notifications\\Channels\\BroadcastChannel
 */',
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'createMailDriver' => 
      array (
        'name' => 'createMailDriver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create an instance of the mail driver.
 *
 * @return \\Illuminate\\Notifications\\Channels\\MailChannel
 */',
        'startLine' => 99,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'createDriver' => 
      array (
        'name' => 'createDriver',
        'parameters' => 
        array (
          'driver' => 
          array (
            'name' => 'driver',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 37,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new driver instance.
 *
 * @param  string  $driver
 * @return mixed
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 112,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'resolveNotificationSender' => 
      array (
        'name' => 'resolveNotificationSender',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the NotificationSender instance.
 *
 * @return \\Illuminate\\Notifications\\NotificationSender
 */',
        'startLine' => 130,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'getDefaultDriver' => 
      array (
        'name' => 'getDefaultDriver',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default channel driver name.
 *
 * @return string
 */',
        'startLine' => 142,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'deliversVia' => 
      array (
        'name' => 'deliversVia',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default channel driver name.
 *
 * @return string
 */',
        'startLine' => 152,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'deliverVia' => 
      array (
        'name' => 'deliverVia',
        'parameters' => 
        array (
          'channel' => 
          array (
            'name' => 'channel',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 32,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the default channel driver name.
 *
 * @param  string  $channel
 * @return void
 */',
        'startLine' => 163,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
      'locale' => 
      array (
        'name' => 'locale',
        'parameters' => 
        array (
          'locale' => 
          array (
            'name' => 'locale',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 174,
            'endLine' => 174,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the locale of notifications.
 *
 * @param  string  $locale
 * @return $this
 */',
        'startLine' => 174,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Notifications',
        'declaringClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'implementingClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'currentClassName' => 'Illuminate\\Notifications\\ChannelManager',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));