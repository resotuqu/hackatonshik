<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1777029991,
	'meta' => array (
  'cacheVersion' => 'v12-linesToIgnore',
  'phpstanVersion' => '2.1.51',
  'fnsr' => false,
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80505,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, parseModelCastsMethod: false, enableMigrationCache: false, paths: [C:\\Users\\resotuqu\\Herd\\hackatonshik\\app, C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes], level: 5, tmpDir: C:\\Users\\resotuqu\\Herd\\hackatonshik\\storage\\phpstan}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ContractsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\FacadesMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ManagersMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\AuthsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper, arguments: {parser: @currentPhpVersionSimpleDirectParser, parseModelCastsMethod: %parseModelCastsMethod%}}, {class: Larastan\\Larastan\\Properties\\MigrationCache, arguments: {cacheDirectory: %tmpDir%, enabled: %enableMigrationCache%}}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%, treatPhpDocTypesAsCertain: %treatPhpDocTypesAsCertain%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentCollectionMapDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}}}',
  'analysedPaths' => 
  array (
    0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app',
    1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    'C:/Users/resotuqu/Herd/hackatonshik/composer.lock' => '15f017803dbb3f16dd521be7bfe711f92d3cc287bfa10e1baec0caf3250b9069',
  ),
  'composerInstalled' => 
  array (
    'C:/Users/resotuqu/Herd/hackatonshik/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'bacon/bacon-qr-code' => 
        array (
          'pretty_version' => 'v3.1.1',
          'version' => '3.1.1.0',
          'reference' => '4da2233e72eeecd9be3b62e0dc2cc9ed8e2e31c2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../bacon/bacon-qr-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'blade-ui-kit/blade-heroicons' => 
        array (
          'pretty_version' => '2.7.0',
          'version' => '2.7.0.0',
          'reference' => '66fa8ba09dba12e0cdb410b8cb94f3b890eca440',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../blade-ui-kit/blade-heroicons',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'blade-ui-kit/blade-icons' => 
        array (
          'pretty_version' => '1.9.1',
          'version' => '1.9.1.0',
          'reference' => '377eede719f9690b03bbbfd516afef887e27634a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../blade-ui-kit/blade-icons',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v7.20.0',
          'version' => '7.20.0.0',
          'reference' => '81c80677c9ec0ed4ef16b246167f11dec81a6e3d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.14.8',
          'version' => '0.14.8.0',
          'reference' => '63422359a44b7f06cae63c3b429b59e8efcc0629',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dasprid/enum' => 
        array (
          'pretty_version' => '1.0.7',
          'version' => '1.0.7.0',
          'reference' => 'b5874fa9ed0043116c72162ec7f4fb50e02e7cce',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../dasprid/enum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => '1.1.6',
          'version' => '1.1.6.0',
          'reference' => 'd4fe3e6fd9bb9e72557a19674f44d8ac7db4c6ca',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fidry/cpu-core-counter' => 
        array (
          'pretty_version' => '1.3.0',
          'version' => '1.3.0.0',
          'reference' => 'db9508f7b1474469d9d3c53b86f817e344732678',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../fidry/cpu-core-counter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'firebase/php-jwt' => 
        array (
          'pretty_version' => 'v7.0.5',
          'version' => '7.0.5.0',
          'reference' => '47ad26bab5e7c70ae8a6f08ed25ff83631121380',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../firebase/php-jwt',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'reference' => '38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => 'e01f4a821471308ba86aa202fed6698b6b695e3b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.10.0',
          'version' => '7.10.0.0',
          'reference' => 'b51ac707cfa420b7bfd4e4d5e510ba8008e822b4',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '481557b130ef3790cf82b713667b43030dc9c957',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.9.0',
          'version' => '2.9.0.0',
          'reference' => '7d0ed42f28e42d61352a7a79de682e5e67fec884',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.5',
          'version' => '1.0.5.0',
          'reference' => '4f4bbd4e7172148801e76e3decc1e559bdee34e1',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.7',
          'version' => '0.7.0.0',
          'reference' => '610392f38de49a44dab08dc1659960a29874c4b8',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/reflection' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v12.57.0',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '4d7aa5dab42e2a76d99559706022885de0e18e1a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'jfcherng/php-color-output' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '6c7bf16686cc6a291647fcb87491640a2d5edd20',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../jfcherng/php-color-output',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'jfcherng/php-diff' => 
        array (
          'pretty_version' => '6.16.2',
          'version' => '6.16.2.0',
          'reference' => '7f46bcfc582e81769237d0b3f6b8a548efe8799d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../jfcherng/php-diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'jfcherng/php-mb-string' => 
        array (
          'pretty_version' => '2.0.1',
          'version' => '2.0.1.0',
          'reference' => '8407bfefde47849c9e7c9594e6de2ac85a0f845d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../jfcherng/php-mb-string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'jfcherng/php-sequence-matcher' => 
        array (
          'pretty_version' => '4.0.3',
          'version' => '4.0.3.0',
          'reference' => 'd2038ac29627340a7458609072a8ba355e80ec5b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../jfcherng/php-sequence-matcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.9.6',
          'version' => '3.9.6.0',
          'reference' => '9ad17e83e96b63536cb6ac39c3d40d29ff9cf636',
          'type' => 'phpstan-extension',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/boost' => 
        array (
          'pretty_version' => 'v2.4.4',
          'version' => '2.4.4.0',
          'reference' => 'db101b977897e00c6d2e40e9b610591cb0aa277e',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/boost',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/fortify' => 
        array (
          'pretty_version' => 'v1.36.2',
          'version' => '1.36.2.0',
          'reference' => 'b36e0782e6f5f6cfbab34327895a63b7c4c031f9',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/fortify',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v12.57.0',
          'version' => '12.57.0.0',
          'reference' => '63a6ced3db46582b3276e2d03770a6317a94d6e2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/mcp' => 
        array (
          'pretty_version' => 'v0.6.7',
          'version' => '0.6.7.0',
          'reference' => 'c3775e57b95d7eadb580d543689d9971ec8721f2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/mcp',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.6',
          'version' => '1.2.6.0',
          'reference' => 'aa71a01c309e7f66bc2ec4fb1a59291b82eb4abf',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.29.0',
          'version' => '1.29.0.0',
          'reference' => 'bdec963f53172c5e36330f3a400604c69bf02d39',
          'type' => 'project',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.17',
          'version' => '0.3.17.0',
          'reference' => '6a82ac19a28b916ae0885828795dbd4c59d9a818',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/roster' => 
        array (
          'pretty_version' => 'v0.5.1',
          'version' => '0.5.1.0',
          'reference' => '5089de7615f72f78e831590ff9d0435fed0102bb',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/roster',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/sail' => 
        array (
          'pretty_version' => 'v1.57.0',
          'version' => '1.57.0.0',
          'reference' => 'fa8d057b6e9310380ccbc3a209ed7f927d54f648',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/sail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.12',
          'version' => '2.0.12.0',
          'reference' => 'a6abb4e54f6fcd3138120b9ad497f0bd146f9919',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/socialite' => 
        array (
          'pretty_version' => 'v5.26.1',
          'version' => '5.26.1.0',
          'reference' => 'db6ec2ee967b7f06412c3a0cf1daaf072f4752a4',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/socialite',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v2.11.1',
          'version' => '2.11.1.0',
          'reference' => 'c9f80cc835649b5c1842898fb043f8cc098dd741',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.8.2',
          'version' => '2.8.2.0',
          'reference' => '59fb075d2101740c337c7216e3f32b36c204218b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.33.0',
          'version' => '3.33.0.0',
          'reference' => '570b8871e0ce693764434b29154c54b434905350',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.31.0',
          'version' => '3.31.0.0',
          'reference' => '2f669db18a4c20c755c2bb7d3a7b0b2340488079',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.16.0',
          'version' => '1.16.0.0',
          'reference' => '2d6702ff215bf922936ccc1ad31007edc76451b9',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/oauth1-client' => 
        array (
          'pretty_version' => 'v1.11.0',
          'version' => '1.11.0.0',
          'reference' => 'f9c94b088837eb1aae1ad7c4f23eb65cc6993055',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/oauth1-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '08cf38e3924d4f56238125547b5720496fac8fd4',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '85d5c77c5d6d3af6c54db4a78246364908f3c928',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'livewire/blaze' => 
        array (
          'pretty_version' => 'v1.0.11',
          'version' => '1.0.11.0',
          'reference' => 'e5984922e09ffb2b1da28e255715630ca67c81e2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../livewire/blaze',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'livewire/livewire' => 
        array (
          'pretty_version' => 'v4.2.4',
          'version' => '4.2.4.0',
          'reference' => '7d0bfa46269b1ec186b8cdd38baffee5cc647d10',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../livewire/livewire',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'b321dd6749f0bf7189444158a3ce785cc16d69b0',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.11.4',
          'version' => '3.11.4.0',
          'reference' => 'e890471a3494740f7d9326d72ce6a8c559ffee60',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.5',
          'version' => '1.3.5.0',
          'reference' => 'f0ab1a3cda782dbc5da270d28545236aa80c4002',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.3',
          'version' => '4.1.3.0',
          'reference' => 'bb3ea637e3d131d72acc033cfc2746ee893349fe',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.7.0',
          'version' => '5.7.0.0',
          'reference' => 'dca41cd15c2ac9d055ad70dbfd011130757d1f82',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.9.3',
          'version' => '8.9.3.0',
          'reference' => 'b0d8ab95b29c3189aeeb902d81215231df4c1b64',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => '712a31b768f5daea284c2169a7d227031001b9a8',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'paragonie/constant_time_encoding' => 
        array (
          'pretty_version' => 'v3.1.3',
          'version' => '3.1.3.0',
          'reference' => 'd5b01a39b3415c2cd581d3bd3a3575c1ebbd8e77',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../paragonie/constant_time_encoding',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'paragonie/random_compat' => 
        array (
          'pretty_version' => 'v9.99.100',
          'version' => '9.99.100.0',
          'reference' => '996434e5492cb4c3edcb9168db6fbb1359ef965a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../paragonie/random_compat',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pestphp/pest' => 
        array (
          'pretty_version' => 'v4.6.3',
          'version' => '4.6.3.0',
          'reference' => 'bff44562a99d30aa37573995566051b0344f9f8e',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'reference' => '9d4b93d7f73d3f9c3189bb22c220fef271cdf568',
          'type' => 'composer-plugin',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest-plugin',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-arch' => 
        array (
          'pretty_version' => 'v4.0.2',
          'version' => '4.0.2.0',
          'reference' => '3fb0d02a91b9da504b139dc7ab2a31efb7c3215c',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest-plugin-arch',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-laravel' => 
        array (
          'pretty_version' => 'v4.1.0',
          'version' => '4.1.0.0',
          'reference' => '3057a36669ff11416cc0dc2b521b3aec58c488d0',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest-plugin-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-mutate' => 
        array (
          'pretty_version' => 'v4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'd9b32b60b2385e1688a68cc227594738ec26d96c',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest-plugin-mutate',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-profanity' => 
        array (
          'pretty_version' => 'v4.2.1',
          'version' => '4.2.1.0',
          'reference' => '343cfa6f3564b7e35df0ebb77b7fa97039f72b27',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pestphp/pest-plugin-profanity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => '7bae67520aa9f5ecc506d646810bd40d9da54582',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '2.0.0',
          'version' => '2.0.0.0',
          'reference' => '327a05bbee54120d4786a0dc67aad30226ad4cf9',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.5',
          'version' => '1.9.5.0',
          'reference' => '75365b91986c2405cf5e1e012c5595cd487a98be',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpseclib/phpseclib' => 
        array (
          'pretty_version' => '3.0.51',
          'version' => '3.0.51.0',
          'reference' => 'd59c94077f9c9915abb51ddb52ce85188ece1748',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpseclib/phpseclib',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '2.3.2',
          'version' => '2.3.2.0',
          'reference' => 'a004701b11273a26cd7955a61d67a7f1e525a45a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.1.51',
          'version' => '2.1.51.0',
          'reference' => 'dc3b523c45e714c70de2ac5113b958223b55dc59',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '12.5.6',
          'version' => '12.5.6.0',
          'reference' => '876099a072646c7745f673d7aeab5382c4439691',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => '3d1cd096ef6bea4bf2762ba586e35dbd317cbfd5',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '12b54e689b07a25a9b41e57736dfab6ec9ae5406',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'e1367a453f0eda562eedb4f659e13aa900d66c53',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '8.0.0',
          'version' => '8.0.0.0',
          'reference' => 'f258ce36aa457f3aa3339f9ed4c81fc66dc8c2cc',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '12.5.23',
          'version' => '12.5.23.0',
          'reference' => 'c54fcf3d6bcb6e96ac2f7e40097dc37b5f139969',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pragmarx/google2fa' => 
        array (
          'pretty_version' => 'v9.0.0',
          'version' => '9.0.0.0',
          'reference' => 'e6bc62dd6ae83acc475f57912e27466019a1f2cf',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../pragmarx/google2fa',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1|2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
            1 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0|2.0|3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.22',
          'version' => '0.12.22.0',
          'reference' => '3be75d5b9244936dd4ac62ade2bfb004d13acf0f',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.2',
          'version' => '4.9.2.0',
          'reference' => '8429c78ca35a09f27565311b98101e2826affde0',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.2',
          ),
        ),
        'robsontenorio/mary' => 
        array (
          'pretty_version' => '2.8.2',
          'version' => '2.8.2.0',
          'reference' => 'fee6e23a158c54c682b5dd5d7c9377ba4889761a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../robsontenorio/mary',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '4.2.0',
          'version' => '4.2.0.0',
          'reference' => '90f41072d220e5c40df6e8635f5dafba2d9d4d04',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '7.1.6',
          'version' => '7.1.6.0',
          'reference' => 'c769009dee98f494e0edc3fd4f4087501688f11e',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'bad4316aba5303d0221f43f8cee37eb58d384bbb',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '7ab1ea946c012266ca32390913653d844ecd085f',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '8.1.0',
          'version' => '8.1.0.0',
          'reference' => 'b121608b28a13f721e76ffbbd386d08eff58f3f6',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '7.0.2',
          'version' => '7.0.2.0',
          'reference' => '016951ae10980765e4e7aee491eb288c64e505b7',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '8.0.2',
          'version' => '8.0.2.0',
          'reference' => 'ef1377171613d09edd25b7816f05be8313f9115d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '4.0.0',
          'version' => '4.0.0.0',
          'reference' => '97ffee3bcfb5805568d6af7f0f893678fc076d2f',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '1effe8e9b8e068e9ae228e542d5d11b5d16db894',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => '4bfa827c969c98be1e527abd576533293c634f6a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '0b01998a7d5b1f122911a66bebcb8d46f0c82d8c',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => 'e549163b9760b8f71f191651d22acf32d56d6d4d',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '3e6ccf7657d4f0a59200564b08cead899313b53c',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'socialiteproviders/manager' => 
        array (
          'pretty_version' => '4.9.2',
          'version' => '4.9.2.0',
          'reference' => '35372dc62787e61e91cfec73f45fd5d5ae0f8891',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../socialiteproviders/manager',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'socialiteproviders/vkontakte' => 
        array (
          'pretty_version' => '5.1.0',
          'version' => '5.1.0.0',
          'reference' => '306a746967d1712c4414c405cd359fe27193f1d1',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../socialiteproviders/vkontakte',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'socialiteproviders/yandex' => 
        array (
          'pretty_version' => '4.1.0',
          'version' => '4.1.0.0',
          'reference' => 'e8cf2c088feda1525b49cee396bd46fc96509f20',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../socialiteproviders/yandex',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/commonmark-shiki-highlighter' => 
        array (
          'pretty_version' => '2.5.2',
          'version' => '2.5.2.0',
          'reference' => 'ef23368cff226658e9a348fd839b33ae6d95d2c6',
          'type' => 'commonmark-extension',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../spatie/commonmark-shiki-highlighter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-markdown' => 
        array (
          'pretty_version' => '2.8.0',
          'version' => '2.8.0.0',
          'reference' => 'eabe8c7e31c2739ad0fe63ba04eb2e3189608187',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../spatie/laravel-markdown',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/laravel-package-tools' => 
        array (
          'pretty_version' => '1.93.0',
          'version' => '1.93.0.0',
          'reference' => '0d097bce95b2bf6802fb1d83e1e753b0f5a948e7',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../spatie/laravel-package-tools',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'spatie/shiki-php' => 
        array (
          'pretty_version' => '2.3.3',
          'version' => '2.3.3.0',
          'reference' => '9d50ff4d9825d87d3283a6695c65ae9c3c3caa6b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../spatie/shiki-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => 'b55a638b189a6faa875e0ccdb00908fb87af95b3',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '1e92e39c51f95b88e3d66fa2d9f06d1fb45dd707',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => '8db1c00226a94d8ab6aa89d9224eeee91e2ea2ed',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '63afe740e99a13ba87ec199bb07bbdee937a5b62',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '8dd79d8af777ee6cba2fd4d98da6ffb839f3c0fa',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => 'f662acc6ab22a3d6d716dcb44c381c6002940df6',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '59eb412e93815df44f05f342958efa9f46b1e586',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => 'e0be088d22278583a82da281886e8c3592fbf149',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '9381209597ec66c25be154cbf2289076e64d1eab',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '017e76ad089bac281553389269e259e155935e1a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => 'f6ea532250b476bfc1b56699b388a1bdbf168f62',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '6df02f99998081032da3407a8d6c4e1dcb5d4379',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '141046a8f9477948ff284fa65be2095baafb94f2',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => 'ad1b7b9092976d6c948b8a187cec9faaea9ec1df',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '9614ac4d8061dc257ecc64cba1b140873dce8ad3',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '6a21eb99c6973357967f6ce3708cd55a6bec6315',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => 'dfb55726c3a76ea3b6459fcfda1ec2d80a682411',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '3600c2cb22399e25bb226e4a135ce91eeb2a6149',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '88486db2c389b290bf87ff1de7ebc1e13e42bb06',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '2c408a6bb0313e6001a83628dc5506100474254e',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.36.0',
          'version' => '1.36.0.0',
          'reference' => '26dfec253c4cf3e51b541b52ddf7e42cb0908e94',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '60f19cd3badc8de688421e21e4305eba50f8089a',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '9608de9873ec86e754fb6c0a0fa7e5f1a960eb6b',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '45112560a3ba2d715666a509a0bc9521d10b6c43',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => 'ae9488f874d7603f9d2dfbf120203882b645d963',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v8.0.8',
          'version' => '8.0.8.0',
          'reference' => '27c03ae3940de24ba2f71cfdbac824f2aa1fdf2f',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '65a8bc82080447fae78373aa10f8d13b38338977',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '6883ebdf7bf6a12b37519dbc0df62b0222401b56',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '9510c3966f749a1d1ff0059e1eabef6cc621e7fd',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/yaml' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => 'c58fdf7b3d6c2995368264c49e4e8b05bcff2883',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../symfony/yaml',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'ta-tikoma/phpunit-architecture-test' => 
        array (
          'pretty_version' => '0.8.7',
          'version' => '0.8.7.0',
          'reference' => '1248f3f506ca9641d4f68cebcd538fa489754db8',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../ta-tikoma/phpunit-architecture-test',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '2.0.1',
          'version' => '2.0.1.0',
          'reference' => '7989e43bf381af0eac72e4f0ca5bcbfa81658be4',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'f0292ccf0ec75843d65027214426b6b163b48b41',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.3',
          'version' => '5.6.3.0',
          'reference' => '955e7815d677a3eaa7075231212f2110983adecc',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => 'd870a33f0f79d2b4579740b0620200221ee44aeb',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => 'eb0d790f735ba6cff25c683a85a1da0eadeff9e4',
          'type' => 'library',
          'install_path' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    'C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\larastan\\larastan\\bootstrap.php' => '5a3eacbf63b3e41659adfee92facededf8e020a932800f93c9a8b0e67f235805',
    'phar://C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\Attribute85.php' => 'cb8b31e82c61ce197871c9e8a6f122256751f2ab606dd2be90846d4fa5f8933e',
    'phar://C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionAttribute.php' => 'c0068e383717870a304781d462f7e2afe1c6f24e9133851852a2aca96b4fa26f',
    'phar://C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionIntersectionType.php' => '65fe0a8bc6fe285d8ddc8798ab5b9299920af70db5ad74596bc08df823e7c5d9',
    'phar://C:\\Users\\resotuqu\\Herd\\hackatonshik\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionUnionType.php' => '1e2fe940e4ba4e00d9ee6adb2af3ee1bf333e6f8afe61c61deb038886d293427',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'FFI',
    2 => 'PDO',
    3 => 'Phar',
    4 => 'Reflection',
    5 => 'SPL',
    6 => 'SimpleXML',
    7 => 'Zend OPcache',
    8 => 'bcmath',
    9 => 'bz2',
    10 => 'calendar',
    11 => 'ctype',
    12 => 'curl',
    13 => 'date',
    14 => 'dom',
    15 => 'exif',
    16 => 'fileinfo',
    17 => 'filter',
    18 => 'gd',
    19 => 'gmp',
    20 => 'hash',
    21 => 'herd',
    22 => 'iconv',
    23 => 'intl',
    24 => 'json',
    25 => 'lexbor',
    26 => 'libxml',
    27 => 'mbstring',
    28 => 'mongodb',
    29 => 'mysqli',
    30 => 'mysqlnd',
    31 => 'openssl',
    32 => 'pcre',
    33 => 'pdo_mysql',
    34 => 'pdo_pgsql',
    35 => 'pdo_sqlite',
    36 => 'pgsql',
    37 => 'random',
    38 => 'readline',
    39 => 'redis',
    40 => 'session',
    41 => 'shmop',
    42 => 'soap',
    43 => 'sockets',
    44 => 'sodium',
    45 => 'sqlite3',
    46 => 'standard',
    47 => 'tokenizer',
    48 => 'uri',
    49 => 'xml',
    50 => 'xmlreader',
    51 => 'xmlwriter',
    52 => 'zip',
    53 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '5',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #1 $announcement of class App\\Notifications\\HackatonAnnouncementPublished constructor expects App\\Models\\HackatonAnnouncement, Illuminate\\Database\\Eloquent\\Model given.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
       'line' => 47,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 47,
       'nodeType' => 'PhpParser\\Node\\Expr\\New_',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method isPending() on string.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
       'line' => 53,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 53,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 65,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 65,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 66,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 66,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 67,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 67,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$type.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 71,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 71,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::answers().',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 78,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 78,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 79,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 79,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 107,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 107,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 109,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 109,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$is_required.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 111,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 111,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 112,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 112,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$label.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 112,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 112,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    11 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$type.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 115,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 115,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    12 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 116,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 116,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    13 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$label.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'line' => 116,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 116,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Http\\Controllers\\HackatonController::resolveAvailableTeams() should return Illuminate\\Database\\Eloquent\\Collection but returns Illuminate\\Support\\Collection<(int|string), mixed>.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'line' => 31,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 31,
       'nodeType' => 'PhpParser\\Node\\Stmt\\Return_',
       'identifier' => 'return.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Method App\\Http\\Controllers\\HackatonController::resolveSubmitterTeams() should return Illuminate\\Database\\Eloquent\\Collection but returns Illuminate\\Support\\Collection<(int|string), mixed>.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'line' => 40,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 40,
       'nodeType' => 'PhpParser\\Node\\Stmt\\Return_',
       'identifier' => 'return.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$roles.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'line' => 62,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 62,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'line' => 62,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 62,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method isPending() on string.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
       'line' => 55,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 55,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
       'line' => 51,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 51,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'The @use tag of class App\\Models\\HackatonApplication describes Illuminate\\Database\\Eloquent\\Factories\\HasFactory but the class uses App\\Models\\Concerns\\HasApplicationReview.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
       'line' => 17,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 17,
       'nodeType' => 'PhpParser\\Node\\Stmt\\TraitUse',
       'identifier' => 'generics.wrongParent',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'The @use tag of class App\\Models\\TeamApplication describes Illuminate\\Database\\Eloquent\\Factories\\HasFactory but the class uses App\\Models\\Concerns\\HasApplicationReview.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
       'line' => 17,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 17,
       'nodeType' => 'PhpParser\\Node\\Stmt\\TraitUse',
       'identifier' => 'generics.wrongParent',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$title.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'line' => 27,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 27,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$title.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'line' => 28,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 28,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$title.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 38,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method toIso8601String() on string.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'line' => 41,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 41,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Using nullsafe method call on non-nullable type string. Use -> instead.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'line' => 41,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 41,
       'nodeType' => 'PhpParser\\Node\\Expr\\NullsafeMethodCall',
       'identifier' => 'nullsafe.neverNull',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
       'line' => 23,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 23,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
       'line' => 28,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 28,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'line' => 24,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 24,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'line' => 24,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 24,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'line' => 21,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 21,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'line' => 31,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 31,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'line' => 36,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 36,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 14,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 14,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::teams().',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 20,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 20,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$hackaton.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 34,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 34,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::roles().',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 47,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 46,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 49,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 49,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$hackaton.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'line' => 54,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 54,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'line' => 30,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 30,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$team.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$team.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
       'line' => 24,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 24,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #1 $view of function view expects view-string|null, string given.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
       'line' => 33,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 33,
       'nodeType' => 'PhpParser\\Node\\Expr\\FuncCall',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Parameter #1 $view of function view expects view-string|null, string given.',
       'file' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
       'line' => 34,
       'canBeIgnored' => true,
       'filePath' => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 34,
       'nodeType' => 'PhpParser\\Node\\Expr\\FuncCall',
       'identifier' => 'argument.type',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\PasswordValidationRules.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
        1 => 7,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector' => 
    array (
      0 => 
      array (
        0 => 'The provided password does not match your current password.',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Enums\\ApplicationStatus.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Enums\\ApplicationStatus',
        1 => 'label',
        2 => 'App\\Enums\\ApplicationStatus',
      ),
      1 => 
      array (
        0 => 'App\\Enums\\ApplicationStatus',
        1 => 'isPending',
        2 => 'App\\Enums\\ApplicationStatus',
      ),
      2 => 
      array (
        0 => 'App\\Enums\\ApplicationStatus',
        1 => 'isAccepted',
        2 => 'App\\Enums\\ApplicationStatus',
      ),
      3 => 
      array (
        0 => 'App\\Enums\\ApplicationStatus',
        1 => 'isRejected',
        2 => 'App\\Enums\\ApplicationStatus',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 19,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 56,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 54,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 43,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'transaction',
        2 => 48,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 88,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 15,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 33,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 18,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 50,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 23,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 34,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 20,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'transaction',
        2 => 44,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 16,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 34,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 42,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'pages.hackatons.show',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 56,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 82,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 44,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Connection',
        1 => 'transaction',
        2 => 49,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'authorize',
        2 => 104,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'pages.teams.show',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'index',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'create',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
      2 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'store',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
      3 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'edit',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
      4 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'update',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
      5 => 
      array (
        0 => 'App\\Http\\Controllers\\TeamController',
        1 => 'destroy',
        2 => 'App\\Http\\Controllers\\TeamController',
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 48,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 52,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonAnnouncementRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonAnnouncementRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreHackatonAnnouncementRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
        1 => 'after',
        2 => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonCaseRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreHackatonCaseRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCertificateRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonCertificateRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreHackatonCertificateRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\StoreHackatonRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreHackatonRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreHackatonRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
        1 => 'after',
        2 => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\StoreTeamRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreTeamRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRoleRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamRoleRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\StoreTeamRoleRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreTeamRoleRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreTeamRoleRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateHackatonRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateHackatonRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\UpdateHackatonRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateHackatonRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\UpdateHackatonRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateTeamRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\UpdateTeamRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateTeamRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\UpdateTeamRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRoleRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateTeamRoleRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\UpdateTeamRoleRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\UpdateTeamRoleRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\UpdateTeamRoleRequest',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Concerns\\HasApplicationReview.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Concerns\\HasApplicationReview',
        1 => 10,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonAnnouncement.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonAnnouncement',
        1 => 'casts',
        2 => 'App\\Models\\HackatonAnnouncement',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonApplication',
        1 => 'casts',
        2 => 'App\\Models\\HackatonApplication',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Concerns\\HasApplicationReview',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonCase',
        1 => 'casts',
        2 => 'App\\Models\\HackatonCase',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseAnswer.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonCaseAnswer',
        1 => 'casts',
        2 => 'App\\Models\\HackatonCaseAnswer',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseField.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonCaseField',
        1 => 'allowedTypes',
        2 => 'App\\Models\\HackatonCaseField',
      ),
      1 => 
      array (
        0 => 'App\\Models\\HackatonCaseField',
        1 => 'casts',
        2 => 'App\\Models\\HackatonCaseField',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonCaseSubmission',
        1 => 'casts',
        2 => 'App\\Models\\HackatonCaseSubmission',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCertificate.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\HackatonCertificate',
        1 => 'casts',
        2 => 'App\\Models\\HackatonCertificate',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonDocument.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Role.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Skill.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\TeamApplication',
        1 => 'casts',
        2 => 'App\\Models\\TeamApplication',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Concerns\\HasApplicationReview',
      ),
      1 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRoleSkill.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamSocialLink.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'casts',
        2 => 'App\\Models\\User',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\UserHackatonDocument.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\Notifications\\HackatonAnnouncementPublished',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Notifications\\HackatonAnnouncementPublished',
        1 => 'via',
        2 => 'App\\Notifications\\HackatonAnnouncementPublished',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Bus\\Queueable',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\HackatonAnnouncementPolicy',
        1 => 'create',
        2 => 'App\\Policies\\HackatonAnnouncementPolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\HackatonAnnouncementPolicy',
        1 => 'view',
        2 => 'App\\Policies\\HackatonAnnouncementPolicy',
      ),
      2 => 
      array (
        0 => 'App\\Policies\\HackatonAnnouncementPolicy',
        1 => 'update',
        2 => 'App\\Policies\\HackatonAnnouncementPolicy',
      ),
      3 => 
      array (
        0 => 'App\\Policies\\HackatonAnnouncementPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\HackatonAnnouncementPolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\HackatonApplicationPolicy',
        1 => 'create',
        2 => 'App\\Policies\\HackatonApplicationPolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\HackatonApplicationPolicy',
        1 => 'update',
        2 => 'App\\Policies\\HackatonApplicationPolicy',
      ),
      2 => 
      array (
        0 => 'App\\Policies\\HackatonApplicationPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\HackatonApplicationPolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\HackatonCasePolicy',
        1 => 'view',
        2 => 'App\\Policies\\HackatonCasePolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\HackatonCasePolicy',
        1 => 'create',
        2 => 'App\\Policies\\HackatonCasePolicy',
      ),
      2 => 
      array (
        0 => 'App\\Policies\\HackatonCasePolicy',
        1 => 'update',
        2 => 'App\\Policies\\HackatonCasePolicy',
      ),
      3 => 
      array (
        0 => 'App\\Policies\\HackatonCasePolicy',
        1 => 'delete',
        2 => 'App\\Policies\\HackatonCasePolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\HackatonCaseSubmissionPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\HackatonCaseSubmissionPolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\HackatonCertificatePolicy',
        1 => 'create',
        2 => 'App\\Policies\\HackatonCertificatePolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\HackatonCertificatePolicy',
        1 => 'view',
        2 => 'App\\Policies\\HackatonCertificatePolicy',
      ),
      2 => 
      array (
        0 => 'App\\Policies\\HackatonCertificatePolicy',
        1 => 'download',
        2 => 'App\\Policies\\HackatonCertificatePolicy',
      ),
      3 => 
      array (
        0 => 'App\\Policies\\HackatonCertificatePolicy',
        1 => 'delete',
        2 => 'App\\Policies\\HackatonCertificatePolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\TeamApplicationPolicy',
        1 => 'create',
        2 => 'App\\Policies\\TeamApplicationPolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\TeamApplicationPolicy',
        1 => 'update',
        2 => 'App\\Policies\\TeamApplicationPolicy',
      ),
      2 => 
      array (
        0 => 'App\\Policies\\TeamApplicationPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\TeamApplicationPolicy',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 47,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 48,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 49,
      ),
      3 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 50,
      ),
      4 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 51,
      ),
      5 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Gate',
        1 => 'policy',
        2 => 52,
      ),
      6 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Date',
        1 => 'use',
        2 => 60,
      ),
      7 => 
      array (
        0 => 'Illuminate\\Validation\\Rules\\Password',
        1 => 'defaults',
        2 => 66,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'pages::auth.login',
      1 => 'pages::auth.register',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 42,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 48,
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\View\\Components\\AppBrand.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 'App\\View\\Components\\AppBrand',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\View\\Components\\AppBrand',
        1 => 'render',
        2 => 'App\\View\\Components\\AppBrand',
      ),
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 21,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 22,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 23,
      ),
      3 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 24,
      ),
      4 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 25,
      ),
      5 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 27,
      ),
      6 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 28,
      ),
      7 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 29,
      ),
      8 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 30,
      ),
      9 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 31,
      ),
      10 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 33,
      ),
      11 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 34,
      ),
      12 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 37,
      ),
      13 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 39,
      ),
      14 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 40,
      ),
      15 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 43,
      ),
      16 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 44,
      ),
      17 => 
      array (
        0 => 'Illuminate\\Routing\\Router',
        1 => 'livewire',
        2 => 45,
      ),
      18 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 57,
      ),
      19 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 61,
      ),
    ),
  ),
); },
	'dependencies' => array (
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php' => 
  array (
    'fileHash' => 'cd439cdc651a2126b467e6211a78f44cd0144be7dde5951e63d9761f1b0207d1',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\PasswordValidationRules.php' => 
  array (
    'fileHash' => 'caff154d31bd88353103600fde15591d2134ef515be3299549fe1d1fe1ea0003',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php' => 
  array (
    'fileHash' => 'fb4e481c7bcececcf37a645c0f91b3e474774662384b3a08dc74233d7076b781',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php' => 
  array (
    'fileHash' => 'b1d6c48a50db690f8759b5214fc1e93fbbfc260eab6dc895e7840f6996d0f096',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserProfileInformation.php' => 
  array (
    'fileHash' => '5c713bf8a389099f8ece58b16fa7d39be95bf46b159519631e60a8d79e1ff6bc',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Enums\\ApplicationStatus.php' => 
  array (
    'fileHash' => '15c2ee95fb5681012c3d411dc5cd8866f4d02caa0f644eaad181a11bb317dce8',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateApplicationStatusRequest.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\Controller.php' => 
  array (
    'fileHash' => '25d1c1ef8e6cc8a376553faacfba2b07d9dfaee9bdbb84f14f77517580e9deb1',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php' => 
  array (
    'fileHash' => 'f2ce63aa61830530b0ba979181ea1861bd0b0aeb03a1dbd02ba1d3f9105e5e73',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php' => 
  array (
    'fileHash' => '728d5b4f92e9b14e11b366dc8993c8cdd432dc6dd569beefd58cf90a8c229548',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php' => 
  array (
    'fileHash' => '3ce96d8dfa2e10e59d487b1510a488688f0e4d9e5958a8d3170d2ef16c1551b5',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php' => 
  array (
    'fileHash' => 'bc6618eb2b5877e832fb7ad711ad16689da7cedb2c986069906b572810309983',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php' => 
  array (
    'fileHash' => '0184f7612d58655c042afc302bb721c4ee4b794d5609e627638dec9414572b3a',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php' => 
  array (
    'fileHash' => 'befe25dcc44ce2470d9998e463700321a5dd8ce11bbe4eb5bc077b93279d0b2d',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php' => 
  array (
    'fileHash' => 'd629cd9e7dccd827086bd18d28494ed15430cfaf6ac893165abadfc103894582',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php' => 
  array (
    'fileHash' => 'c5be747d4a35966f8a5ab2ab992d6646b622a25c7e58d1919a55d1f843fdea3e',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php' => 
  array (
    'fileHash' => '57615651ce950998bd5a7bcefda036bb043f5b181efa5da434766993f80a42e1',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonAnnouncementRequest.php' => 
  array (
    'fileHash' => 'bf86f40a495615eb8967998b1f0f6025972db8834f3e49c8c3e81862b42ffbc2',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php' => 
  array (
    'fileHash' => '011e5e46f672f4bd9c9d20f94ba075bc4e7184174896495036cadbadd1af6323',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseFieldRequest.php' => 
  array (
    'fileHash' => '82c54b70720ea76ff23f8ce9215c72017dae3605b90db4ee03076d2ada96f564',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseRequest.php' => 
  array (
    'fileHash' => '8941805ea2adb12bf8250f383004b9c3ad2b8d4816733f7de0a4b6afeaf75073',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseSubmissionRequest.php' => 
  array (
    'fileHash' => 'cea6fa30788df84073e13d1d16237b1061d610ebba884dafeed280d9e7715ef3',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCertificateRequest.php' => 
  array (
    'fileHash' => '8cbf01ba73bfdf7566b9fdb20f88006ff1f1138e195d2ccf430b815aef3c0ba1',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonRequest.php' => 
  array (
    'fileHash' => 'a2883afb24b8055c6da24d79494d7ab5f040801ae2dfd7ac535e0991eacee8fc',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php' => 
  array (
    'fileHash' => 'ad416e431cf044c87edd826a219cf3dbe89445eb682f72a3d624d50f6225211b',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRequest.php' => 
  array (
    'fileHash' => '570293d2c539155a09f5850045f7292d997851c2bac7bda0c3d1748787c0f4b0',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRoleRequest.php' => 
  array (
    'fileHash' => '5f2d4894178157b148c819adcdc47d6c51b6f0ec947f13eef045a95078b75117',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateApplicationStatusRequest.php' => 
  array (
    'fileHash' => '0733799c4375b5de44c1e7f5d7923d57fc31886f6376029985edbe96716fc62f',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateHackatonRequest.php' => 
  array (
    'fileHash' => 'f1f243a57a33315f2d2cd48bcd360342123632be28eef8d47402364d9528043f',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRequest.php' => 
  array (
    'fileHash' => 'be031e9ae1b396ce29482beed10e4cf17eefce032780e36397c70e393d1456c4',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRoleRequest.php' => 
  array (
    'fileHash' => 'd9e7a0fd131ff58ec354b3f2fd60b4603d2de845e8366a322dee8504610959ae',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Concerns\\HasApplicationReview.php' => 
  array (
    'fileHash' => 'caf711b70103744375521c36dbf76144ffe9a7420b7b8555353d4bddd7a7f93f',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
      10 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
      11 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
      12 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php' => 
  array (
    'fileHash' => 'f8df2d7b26d6c4833624cab24d7a8a85cb6f59ebe570636cd9d9ad7563644c9c',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonAnnouncement.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php',
      10 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCertificate.php',
      11 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonDocument.php',
      12 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      13 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
      14 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
      15 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
      16 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonAnnouncement.php' => 
  array (
    'fileHash' => '35d6ba39977702ac35961880b2d1e6bc0735e77c2dccf4aecfd142073a8ea5b9',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonAnnouncementRequest.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php' => 
  array (
    'fileHash' => 'e3dc8be31cc3b859a8149aedf4f430d784f374bd1557cc37d0406feaaa57b898',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php' => 
  array (
    'fileHash' => '8a15c43973d53cb3283cd89e7abce38d914bf492bed4aaec334ffe3ee14136d5',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseRequest.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseField.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseAnswer.php' => 
  array (
    'fileHash' => '0829b738babd323f94ac4df4000a3e3361ee07af97bb1d3a01f03e56df98411b',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseField.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseField.php' => 
  array (
    'fileHash' => 'a7ed3320a8a474c8fbec7127042ae38de5bc871689bb220cef2efe0f64449fec',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseFieldRequest.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseAnswer.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php' => 
  array (
    'fileHash' => '2fb3903cfe60e3efdc56ca0f4c5e1b4d8ee5fd30cb4b9845eaa1ebadde013b22',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseSubmissionRequest.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseAnswer.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCertificate.php' => 
  array (
    'fileHash' => '06cae7e3de510b726dc307a90b9c00abc269c2d44bc07be999387078d3120b02',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCertificateRequest.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonDocument.php' => 
  array (
    'fileHash' => 'a877b34db5cc551fef49bce62edc4608b5888055a4ade190049ec01652d60da4',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\UserHackatonDocument.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Role.php' => 
  array (
    'fileHash' => 'c7dfaaaaf5ab14808a53b94f5081dfbca583b98034a664456c858aebf7e3939b',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Skill.php' => 
  array (
    'fileHash' => '9c97defc5c00922445a9fe4517e74b00bbdffdc44650664d149a736ddb4c2008',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRoleSkill.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php' => 
  array (
    'fileHash' => 'a8cfbefe74f8b2e04bf150e1a4c42b2174675e28fba72c751105aecf49a6bb89',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamSocialLink.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php' => 
  array (
    'fileHash' => 'ca44455b9fcc68abe1efb3af13cc23fae7757111f569979396e139e68179d2c5',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php' => 
  array (
    'fileHash' => '236022dbbd910e0d15920385ced4cd8331ad3f03aa8e9949f16e4747c518adea',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Role.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Skill.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRoleSkill.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRoleSkill.php' => 
  array (
    'fileHash' => '6fece34476e6ad3f65b03f3f53bb253d2e8fbb82838ba77e9202d8110964c037',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamSocialLink.php' => 
  array (
    'fileHash' => 'd625d5dad4854dfb649f2bfcfc96524bc732e243a23aebfaa0c383e594096226',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php' => 
  array (
    'fileHash' => 'bdade70a4d232159e036a9c8cc8ce21e7648d924f703cce18720affa80c98b87',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php',
      3 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserProfileInformation.php',
      4 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
      5 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php',
      6 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php',
      7 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php',
      8 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php',
      9 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php',
      10 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonAnnouncementRequest.php',
      11 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php',
      12 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseFieldRequest.php',
      13 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseRequest.php',
      14 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseSubmissionRequest.php',
      15 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCertificateRequest.php',
      16 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php',
      17 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      18 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonAnnouncement.php',
      19 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php',
      20 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php',
      21 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCertificate.php',
      22 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php',
      23 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php',
      24 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php',
      25 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\UserHackatonDocument.php',
      26 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php',
      27 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php',
      28 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php',
      29 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php',
      30 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php',
      31 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php',
      32 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\UserHackatonDocument.php' => 
  array (
    'fileHash' => '97b984353e5d8e9cd3913344d57dd32698d7751036c455bab825f03b1e9ceb33',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php',
      1 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonDocument.php',
      2 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php' => 
  array (
    'fileHash' => '2cd21c143a4cf2d6da84c9c76f13e81ce9169811c4e4a4d1885a7a58731dbf24',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php' => 
  array (
    'fileHash' => '6ae039457fec225bd9e56a478469bb3ce1524f9de90ac194f708a5dc4e267bf8',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php' => 
  array (
    'fileHash' => 'b0236d4b3a7711e90c44bb28d76745888a7ac7a44c1de05e7ba369aa08147884',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php' => 
  array (
    'fileHash' => 'ae4558a36da603aba1a369968d7b6bf0aafa8c4759d8b44fcee9fce88be8384e',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php' => 
  array (
    'fileHash' => '8c7726c4176affd5e4189854861d262d1c67a9a98d12a69a4dc803c683f79292',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php' => 
  array (
    'fileHash' => 'd6cc7320ebe8a03f13eb39d90fb0342582e19da43b982e24112d291897d5ad07',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php' => 
  array (
    'fileHash' => 'ae557cb6416b59cc4430012d4e48ba0ce3a9496ff822e7a07d40b3079c5d38b8',
    'dependentFiles' => 
    array (
      0 => 'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'fileHash' => '8fde78807b97d1c6c84d8382053c38c25d7925d48e825ef34bf6fd3659bcee39',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php' => 
  array (
    'fileHash' => 'c41684165914f87dfa58e2d353acbe9556e2b993d88b0fec235dee7f1350778c',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\View\\Components\\AppBrand.php' => 
  array (
    'fileHash' => '1c5510b6133483c3c0bb66162bfe60e7c26ebf9da43b96ea1a9912903abd20cf',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\console.php' => 
  array (
    'fileHash' => '9adccc33e7dd400683e434774077c7fdb2f299c5712cedf16a43fdf56f2850fa',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\routes\\web.php' => 
  array (
    'fileHash' => 'b600ffb4729e2760d66718a818ee79e1e863b29fad13a5c84444239da68ca9e7',
    'dependentFiles' => 
    array (
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\CreateNewUser.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\Fortify\\CreateNewUser',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Laravel\\Fortify\\Contracts\\CreatesNewUsers',
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'validator' => 'Illuminate\\Support\\Facades\\Validator',
              'rule' => 'Illuminate\\Validation\\Rule',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'createsnewusers' => 'Laravel\\Fortify\\Contracts\\CreatesNewUsers',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Models\\User',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\PasswordValidationRules.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Actions\\Fortify\\PasswordValidationRules',
       'phpDoc' => NULL,
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'passwordRules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \\Illuminate\\Contracts\\Validation\\Rule|array<mixed>|string>
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'password' => 'Illuminate\\Validation\\Rules\\Password',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\ResetUserPassword.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\Fortify\\ResetUserPassword',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Laravel\\Fortify\\Contracts\\ResetsUserPasswords',
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'reset',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Validate and reset the user\'s forgotten password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'validator' => 'Illuminate\\Support\\Facades\\Validator',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'resetsuserpasswords' => 'Laravel\\Fortify\\Contracts\\ResetsUserPasswords',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserPassword.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\Fortify\\UpdateUserPassword',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Laravel\\Fortify\\Contracts\\UpdatesUserPasswords',
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Actions\\Fortify\\PasswordValidationRules',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Validate and update the user\'s password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'validator' => 'Illuminate\\Support\\Facades\\Validator',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'updatesuserpasswords' => 'Laravel\\Fortify\\Contracts\\UpdatesUserPasswords',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Actions\\Fortify\\UpdateUserProfileInformation.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\Fortify\\UpdateUserProfileInformation',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Laravel\\Fortify\\Contracts\\UpdatesUserProfileInformation',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Validate and update the given user\'s profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
              'validator' => 'Illuminate\\Support\\Facades\\Validator',
              'rule' => 'Illuminate\\Validation\\Rule',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'updatesuserprofileinformation' => 'Laravel\\Fortify\\Contracts\\UpdatesUserProfileInformation',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'updateVerifiedUser',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Update the given verified user\'s profile information.
     *
     * @param  array<string, string>  $input
     */',
             'namespace' => 'App\\Actions\\Fortify',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'mustverifyemail' => 'Illuminate\\Contracts\\Auth\\MustVerifyEmail',
              'validator' => 'Illuminate\\Support\\Facades\\Validator',
              'rule' => 'Illuminate\\Validation\\Rule',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'updatesuserprofileinformation' => 'Laravel\\Fortify\\Contracts\\UpdatesUserProfileInformation',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'input',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Enums\\ApplicationStatus.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Enums\\ApplicationStatus',
       'scalarType' => 'string',
       'phpDoc' => NULL,
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'PENDING',
           'value' => '\'pending\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'ACCEPTED',
           'value' => '\'accepted\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'REJECTED',
           'value' => '\'rejected\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isPending',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isAccepted',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isRejected',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\Controller.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Controller',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonAnnouncementController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonAnnouncementController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonAnnouncementRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'announcement',
               'type' => 'App\\Models\\HackatonAnnouncement',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonApplicationController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonApplicationController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\UpdateApplicationStatusRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\HackatonApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\HackatonApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonCaseController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonCaseRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'case',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseFieldController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonCaseFieldController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonCaseFieldRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'case',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'case',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'field',
               'type' => 'App\\Models\\HackatonCaseField',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCaseSubmissionController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonCaseSubmissionController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonCaseSubmissionRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'case',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonCertificateController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonCertificateController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreHackatonCertificateRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'download',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'mixed',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'certificate',
               'type' => 'App\\Models\\HackatonCertificate',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'certificate',
               'type' => 'App\\Models\\HackatonCertificate',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\HackatonController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\HackatonController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'show',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamApplicationController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\TeamApplicationController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\UpdateApplicationStatusRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\TeamApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\TeamApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Controllers\\TeamController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\TeamController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'index',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Display a listing of the resource.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Show the form for creating a new resource.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Store a newly created resource in storage.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\StoreTeamRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'show',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'team',
               'type' => 'App\\Models\\Team',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroyParticipant',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'team',
               'type' => 'App\\Models\\Team',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'teamRole',
               'type' => 'App\\Models\\TeamRole',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'edit',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Show the form for editing the specified resource.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'team',
               'type' => 'App\\Models\\Team',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Update the specified resource in storage.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\UpdateTeamRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'team',
               'type' => 'App\\Models\\Team',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Remove the specified resource from storage.
     */',
             'namespace' => 'App\\Http\\Controllers',
             'uses' => 
            array (
              'storeteamrequest' => 'App\\Http\\Requests\\StoreTeamRequest',
              'updateteamrequest' => 'App\\Http\\Requests\\UpdateTeamRequest',
              'team' => 'App\\Models\\Team',
              'teamrole' => 'App\\Models\\TeamRole',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'team',
               'type' => 'App\\Models\\Team',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonAnnouncementRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonAnnouncementRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonApplicationRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonApplicationRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'after',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseFieldRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonCaseFieldRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonCaseRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCaseSubmissionRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonCaseSubmissionRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonCertificateRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonCertificateRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreHackatonRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreHackatonRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamApplicationRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreTeamApplicationRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'after',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreTeamRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\StoreTeamRoleRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreTeamRoleRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateApplicationStatusRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\UpdateApplicationStatusRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateHackatonRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\UpdateHackatonRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\UpdateTeamRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Http\\Requests\\UpdateTeamRoleRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\UpdateTeamRoleRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Concerns\\HasApplicationReview.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Models\\Concerns\\HasApplicationReview',
       'phpDoc' => NULL,
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'markAsAccepted',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reviewer',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'markAsRejected',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reviewer',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Hackaton.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Hackaton',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teams',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamsCount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'participantsCount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'documents',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'usersDocuments',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'applications',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'cases',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'announcements',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'certificates',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'loadShowRelations',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'self',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonAnnouncement.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonAnnouncement',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'author',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonApplication.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonApplication',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Models\\Concerns\\HasApplicationReview',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'team',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'reviewer',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCase.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonCase',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'attributes',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fields',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'submissions',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseAnswer.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonCaseAnswer',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'submission',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'field',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseField.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonCaseField',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'TYPE_TEXT',
               'value' => '\'text\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'TYPE_URL',
               'value' => '\'url\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'TYPE_TEXTAREA',
               'value' => '\'textarea\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'TYPE_FILE',
               'value' => '\'file\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'attributes',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'allowedTypes',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'case',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'answers',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCaseSubmission.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonCaseSubmission',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'case',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'team',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'submittedBy',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'answers',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonCertificate.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonCertificate',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'uploader',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\HackatonDocument.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\HackatonDocument',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'usersFiles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Role.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Role',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamRoles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Skill.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Skill',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamRoles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\Team.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Team',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'socialLinks',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackaton',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'roles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'emptyRoles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'participantsCount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'applications',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasManyThrough',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackatonApplications',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'caseSubmissions',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'loadShowRelations',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'self',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamApplication.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\TeamApplication',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Models\\Concerns\\HasApplicationReview',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamRole',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'reviewer',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRole.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\TeamRole',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'skills',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'team',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'role',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'applications',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamRoleSkill.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\TeamRoleSkill',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'table',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'skill',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamRole',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\TeamSocialLink.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\TeamSocialLink',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'team',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\User.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\User',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Auth\\User',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teams',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackatons',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamRoles',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'userDocuments',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'teamApplications',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'caseSubmissions',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'certificates',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'hidden',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'initials',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the user\'s initials
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Models\\UserHackatonDocument.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\UserHackatonDocument',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'hackatonDocument',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Notifications\\HackatonAnnouncementPublished.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Notifications\\HackatonAnnouncementPublished',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Notifications\\Notification',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Bus\\Queueable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'announcement',
               'type' => 'App\\Models\\HackatonAnnouncement',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'via',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toMail',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Notifications\\Messages\\MailMessage',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonAnnouncementPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\HackatonAnnouncementPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonAnnouncement',
               'type' => 'App\\Models\\HackatonAnnouncement',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonAnnouncement',
               'type' => 'App\\Models\\HackatonAnnouncement',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonAnnouncement',
               'type' => 'App\\Models\\HackatonAnnouncement',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonApplicationPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\HackatonApplicationPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\HackatonApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\HackatonApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCasePolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\HackatonCasePolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => '?App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCase',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCase',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCase',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCaseSubmissionPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\HackatonCaseSubmissionPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCase',
               'type' => 'App\\Models\\HackatonCase',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCaseSubmission',
               'type' => 'App\\Models\\HackatonCaseSubmission',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCaseSubmission',
               'type' => 'App\\Models\\HackatonCaseSubmission',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\HackatonCertificatePolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\HackatonCertificatePolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackaton',
               'type' => 'App\\Models\\Hackaton',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCertificate',
               'type' => 'App\\Models\\HackatonCertificate',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'download',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCertificate',
               'type' => 'App\\Models\\HackatonCertificate',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'hackatonCertificate',
               'type' => 'App\\Models\\HackatonCertificate',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Policies\\TeamApplicationPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\TeamApplicationPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\TeamApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'application',
               'type' => 'App\\Models\\TeamApplication',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\AppServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\AppServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'hackatonannouncement' => 'App\\Models\\HackatonAnnouncement',
              'hackatonapplication' => 'App\\Models\\HackatonApplication',
              'hackatoncase' => 'App\\Models\\HackatonCase',
              'hackatoncasesubmission' => 'App\\Models\\HackatonCaseSubmission',
              'hackatoncertificate' => 'App\\Models\\HackatonCertificate',
              'teamapplication' => 'App\\Models\\TeamApplication',
              'hackatonannouncementpolicy' => 'App\\Policies\\HackatonAnnouncementPolicy',
              'hackatonapplicationpolicy' => 'App\\Policies\\HackatonApplicationPolicy',
              'hackatoncasepolicy' => 'App\\Policies\\HackatonCasePolicy',
              'hackatoncasesubmissionpolicy' => 'App\\Policies\\HackatonCaseSubmissionPolicy',
              'hackatoncertificatepolicy' => 'App\\Policies\\HackatonCertificatePolicy',
              'teamapplicationpolicy' => 'App\\Policies\\TeamApplicationPolicy',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'date' => 'Illuminate\\Support\\Facades\\Date',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'event' => 'Illuminate\\Support\\Facades\\Event',
              'gate' => 'Illuminate\\Support\\Facades\\Gate',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'password' => 'Illuminate\\Validation\\Rules\\Password',
              'socialitewascalled' => 'SocialiteProviders\\Manager\\SocialiteWasCalled',
              'provider' => 'SocialiteProviders\\Yandex\\Provider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'hackatonannouncement' => 'App\\Models\\HackatonAnnouncement',
              'hackatonapplication' => 'App\\Models\\HackatonApplication',
              'hackatoncase' => 'App\\Models\\HackatonCase',
              'hackatoncasesubmission' => 'App\\Models\\HackatonCaseSubmission',
              'hackatoncertificate' => 'App\\Models\\HackatonCertificate',
              'teamapplication' => 'App\\Models\\TeamApplication',
              'hackatonannouncementpolicy' => 'App\\Policies\\HackatonAnnouncementPolicy',
              'hackatonapplicationpolicy' => 'App\\Policies\\HackatonApplicationPolicy',
              'hackatoncasepolicy' => 'App\\Policies\\HackatonCasePolicy',
              'hackatoncasesubmissionpolicy' => 'App\\Policies\\HackatonCaseSubmissionPolicy',
              'hackatoncertificatepolicy' => 'App\\Policies\\HackatonCertificatePolicy',
              'teamapplicationpolicy' => 'App\\Policies\\TeamApplicationPolicy',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'date' => 'Illuminate\\Support\\Facades\\Date',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'event' => 'Illuminate\\Support\\Facades\\Event',
              'gate' => 'Illuminate\\Support\\Facades\\Gate',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'password' => 'Illuminate\\Validation\\Rules\\Password',
              'socialitewascalled' => 'SocialiteProviders\\Manager\\SocialiteWasCalled',
              'provider' => 'SocialiteProviders\\Yandex\\Provider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'configureDefaults',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Configure default behaviors for production-ready applications.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'hackatonannouncement' => 'App\\Models\\HackatonAnnouncement',
              'hackatonapplication' => 'App\\Models\\HackatonApplication',
              'hackatoncase' => 'App\\Models\\HackatonCase',
              'hackatoncasesubmission' => 'App\\Models\\HackatonCaseSubmission',
              'hackatoncertificate' => 'App\\Models\\HackatonCertificate',
              'teamapplication' => 'App\\Models\\TeamApplication',
              'hackatonannouncementpolicy' => 'App\\Policies\\HackatonAnnouncementPolicy',
              'hackatonapplicationpolicy' => 'App\\Policies\\HackatonApplicationPolicy',
              'hackatoncasepolicy' => 'App\\Policies\\HackatonCasePolicy',
              'hackatoncasesubmissionpolicy' => 'App\\Policies\\HackatonCaseSubmissionPolicy',
              'hackatoncertificatepolicy' => 'App\\Policies\\HackatonCertificatePolicy',
              'teamapplicationpolicy' => 'App\\Policies\\TeamApplicationPolicy',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'date' => 'Illuminate\\Support\\Facades\\Date',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'event' => 'Illuminate\\Support\\Facades\\Event',
              'gate' => 'Illuminate\\Support\\Facades\\Gate',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'password' => 'Illuminate\\Validation\\Rules\\Password',
              'socialitewascalled' => 'SocialiteProviders\\Manager\\SocialiteWasCalled',
              'provider' => 'SocialiteProviders\\Yandex\\Provider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\Providers\\FortifyServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\FortifyServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'createnewuser' => 'App\\Actions\\Fortify\\CreateNewUser',
              'resetuserpassword' => 'App\\Actions\\Fortify\\ResetUserPassword',
              'updateuserpassword' => 'App\\Actions\\Fortify\\UpdateUserPassword',
              'updateuserprofileinformation' => 'App\\Actions\\Fortify\\UpdateUserProfileInformation',
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'str' => 'Illuminate\\Support\\Str',
              'redirectiftwofactorauthenticatable' => 'Laravel\\Fortify\\Actions\\RedirectIfTwoFactorAuthenticatable',
              'fortify' => 'Laravel\\Fortify\\Fortify',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'createnewuser' => 'App\\Actions\\Fortify\\CreateNewUser',
              'resetuserpassword' => 'App\\Actions\\Fortify\\ResetUserPassword',
              'updateuserpassword' => 'App\\Actions\\Fortify\\UpdateUserPassword',
              'updateuserprofileinformation' => 'App\\Actions\\Fortify\\UpdateUserProfileInformation',
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'str' => 'Illuminate\\Support\\Str',
              'redirectiftwofactorauthenticatable' => 'Laravel\\Fortify\\Actions\\RedirectIfTwoFactorAuthenticatable',
              'fortify' => 'Laravel\\Fortify\\Fortify',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\Users\\resotuqu\\Herd\\hackatonshik\\app\\View\\Components\\AppBrand.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\View\\Components\\AppBrand',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\View\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Create a new component instance.
     */',
             'namespace' => 'App\\View\\Components',
             'uses' => 
            array (
              'closure' => 'Closure',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'component' => 'Illuminate\\View\\Component',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the view / contents that represent the component.
     */',
             'namespace' => 'App\\View\\Components',
             'uses' => 
            array (
              'closure' => 'Closure',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'component' => 'Illuminate\\View\\Component',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View|Closure|string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
