<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

use Arxy\GraphQLCodegen\BaseModule;
use Arxy\GraphQLCodegen\Generator;
use Arxy\GraphQLCodegen\Module;
use GraphQL\Language\Parser;
use GraphQL\Utils\AST;

$modules = [
    new Module(
        name: 'module1',
        schema: __DIR__ . '/tests/Module1/schema.graphql',
        namespace: 'Arxy\GraphQLCodegen\Tests\Module1\Expected',
        typeMapping: [
            'DateTime' => DateTimeInterface::class,
            'LocalDate' => DateTimeInterface::class,
            'LocalDateTime' => DateTimeInterface::class,
            'Long'=> "int"
        ],
        directory: __DIR__ . '/tests/Module1/Expected'
    )
];

foreach ($modules as $module) {
    file_put_contents(
        dirname($module->getSchema()) . '/ast.php',
        "<?php\n\nreturn " . var_export(
            AST::toArray(
                Parser::parse(
                    file_get_contents(
                        $module->getSchema()
                    )
                )
            ),
            true
        ) . ";\n"
    );
}
$generator = new Generator(
    new BaseModule('Arxy\GraphQLCodegen\Tests\Expected', __DIR__ . '/tests/Expected'),
    $modules
);
$generator->execute();

