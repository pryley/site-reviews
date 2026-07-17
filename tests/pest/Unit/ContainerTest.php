<?php

use GeminiLabs\SiteReviews\Exceptions\BindingResolutionException;

uses()->group('plugin');

/*
 * The DI container (Container.php, which the Application extends). Everything here resolves
 * through glsr() — the real container — with fixture classes declared below, the same way
 * AddonTest declares its fixtures: a container is only provably wrong at its edges, and the
 * edges are the refusals.
 */

interface ContainerTestContract
{
}

interface ContainerTestBoundContract
{
}

class ContainerTestNeedsContract
{
    public function __construct(ContainerTestContract $dependency)
    {
    }
}

class ContainerTestNeedsPrimitive
{
    public function __construct($primitive) // untyped on purpose: typed builtins take the class route
    {
    }
}

class ContainerTestNeedsBuiltin
{
    public function __construct(string $primitive)
    {
    }
}

class ContainerTestOptionalContract
{
    public ?ContainerTestContract $dependency;

    public function __construct(?ContainerTestContract $dependency = null)
    {
        $this->dependency = $dependency;
    }
}

test('an interface with no binding is a refusal that names the target', function () {
    expect(fn () => glsr()->make(ContainerTestContract::class))
        ->toThrow(BindingResolutionException::class, 'Target [ContainerTestContract] is not instantiable.');
});

test('a dependency that cannot be built names the class that wanted it', function () {
    // The build stack is the difference between "X is not instantiable" (so what?) and
    // "X is not instantiable while building Y" (go look at Y's constructor).
    expect(fn () => glsr()->make(ContainerTestNeedsContract::class))
        ->toThrow(BindingResolutionException::class, 'while building [ContainerTestNeedsContract]');
});

test('a primitive parameter with no default cannot be guessed at', function () {
    expect(fn () => glsr()->make(ContainerTestNeedsPrimitive::class))
        ->toThrow(BindingResolutionException::class, 'Unresolvable dependency');

    // a TYPED builtin goes down the class route instead, and fails as a missing class
    expect(fn () => glsr()->make(ContainerTestNeedsBuiltin::class))
        ->toThrow(BindingResolutionException::class, 'Target class [string] does not exist.');
});

test('an optional dependency that cannot be built falls back to its default', function () {
    $instance = glsr()->make(ContainerTestOptionalContract::class);

    expect($instance)->toBeInstanceOf(ContainerTestOptionalContract::class)
        ->and($instance->dependency)->toBeNull();
});

test('an unbuildable dependency can be provided as a parameter override', function () {
    $contract = new class() implements ContainerTestContract {
    };

    $instance = glsr()->make(ContainerTestNeedsContract::class, ['dependency' => $contract]);

    expect($instance)->toBeInstanceOf(ContainerTestNeedsContract::class);
});

test('a binding to an interface makes the interface resolvable', function () {
    // Its own interface, not ContainerTestContract: bindings persist on the singleton
    // container, and the refusal tests above must still refuse under a shuffled run order.
    glsr()->bind(ContainerTestBoundContract::class, fn () => new class() implements ContainerTestBoundContract {
    });

    expect(glsr()->make(ContainerTestBoundContract::class))->toBeInstanceOf(ContainerTestBoundContract::class);
});

test('a class name without its namespace still resolves', function () {
    expect(glsr()->make('Helper'))->toBeInstanceOf(GeminiLabs\SiteReviews\Helper::class);
});
