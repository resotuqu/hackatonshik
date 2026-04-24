<?php declare(strict_types = 1);

// osfsl-C:/Users/resotuqu/Herd/hackatonshik/vendor/composer/../socialiteproviders/yandex/Provider.php-PHPStan\BetterReflection\Reflection\ReflectionClass-SocialiteProviders\Yandex\Provider
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c3e03e5b26acf324affbd2a0f92518ed855b755cb0a2049f8b7aa91389395e1d-8.5.5-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'SocialiteProviders\\Yandex\\Provider',
        'filename' => 'C:/Users/resotuqu/Herd/hackatonshik/vendor/composer/../socialiteproviders/yandex/Provider.php',
      ),
    ),
    'namespace' => 'SocialiteProviders\\Yandex',
    'name' => 'SocialiteProviders\\Yandex\\Provider',
    'shortName' => 'Provider',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 75,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'SocialiteProviders\\Manager\\OAuth2\\AbstractProvider',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'IDENTIFIER' => 
      array (
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'name' => 'IDENTIFIER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'YANDEX\'',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 42,
            'startFilePos' => 297,
            'endTokenPos' => 42,
            'endFilePos' => 304,
          ),
        ),
        'docComment' => '/**
 * Unique Provider Identifier.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'getAuthUrl' => 
      array (
        'name' => 'getAuthUrl',
        'parameters' => 
        array (
          'state' => 
          array (
            'name' => 'state',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 35,
            'endColumn' => 40,
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
 * {@inheritdoc}
 */',
        'startLine' => 19,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'SocialiteProviders\\Yandex',
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'currentClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'aliasName' => NULL,
      ),
      'getTokenUrl' => 
      array (
        'name' => 'getTokenUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * {@inheritdoc}
 */',
        'startLine' => 30,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'SocialiteProviders\\Yandex',
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'currentClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'aliasName' => NULL,
      ),
      'getUserByToken' => 
      array (
        'name' => 'getUserByToken',
        'parameters' => 
        array (
          'token' => 
          array (
            'name' => 'token',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 39,
            'endColumn' => 44,
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
 * {@inheritdoc}
 */',
        'startLine' => 38,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'SocialiteProviders\\Yandex',
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'currentClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'aliasName' => NULL,
      ),
      'mapUserToObject' => 
      array (
        'name' => 'mapUserToObject',
        'parameters' => 
        array (
          'user' => 
          array (
            'name' => 'user',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 55,
            'endLine' => 55,
            'startColumn' => 40,
            'endColumn' => 50,
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
 * {@inheritdoc}
 */',
        'startLine' => 55,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'SocialiteProviders\\Yandex',
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'currentClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'aliasName' => NULL,
      ),
      'getTokenFields' => 
      array (
        'name' => 'getTokenFields',
        'parameters' => 
        array (
          'code' => 
          array (
            'name' => 'code',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 39,
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
 * {@inheritdoc}
 */',
        'startLine' => 69,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'SocialiteProviders\\Yandex',
        'declaringClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'implementingClassName' => 'SocialiteProviders\\Yandex\\Provider',
        'currentClassName' => 'SocialiteProviders\\Yandex\\Provider',
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