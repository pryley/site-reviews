<?php

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The base every command extends.
 *
 * Each concrete command is tested through its own handle(); what is pinned here is the machinery
 * they all inherit and none of them re-implement — the success flag, how a command finds its
 * request, and the JSON envelope it sends the admin JS. It is driven through the smallest possible
 * command: one that does nothing but exist.
 */

class AbstractCommandStub extends AbstractCommand
{
    public $request;

    public function handle(): void
    {
    }
}

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $this->setUpAjax();
});

afterEach(function () {
    $this->tearDownAjax();
    unset($_SERVER['HTTP_REFERER']);
    glsr(Notice::class)->clear();
});

test('a command starts successful, and fail() and pass() flip it', function () {
    $command = new AbstractCommandStub();
    expect($command->successful())->toBeTrue();

    $command->fail();
    expect($command->successful())->toBeFalse();

    $command->pass();
    expect($command->successful())->toBeTrue();
});

test('a command with no request is handed a fresh empty one', function () {
    $command = new AbstractCommandStub();

    expect($command->hasRequest())->toBeFalse()
        ->and($command->request())->toBeInstanceOf(Request::class);
});

test('a command with a request is handed its own', function () {
    $command = new AbstractCommandStub();
    $command->request = $request = new Request(['colour' => 'blue']);

    expect($command->hasRequest())->toBeTrue()
        ->and($command->request())->toBe($request);
});

test('the default response is empty and the default referer is the site home', function () {
    $command = new AbstractCommandStub();

    expect($command->response())->toBe([])
        ->and($command->referer())->toBe(trailingslashit(network_home_url()));
});

test('a failed command with nothing to say still points the admin at the console', function () {
    // The last branch of sendJsonResponse(): the command failed, the request came from the admin
    // (so not the front-end-submission early exit that returns a bare error), and it left no notices
    // of its own. Rather than a silent {success:false}, it adds one telling the person where to look.
    $_SERVER['HTTP_REFERER'] = admin_url('edit.php');
    $command = new AbstractCommandStub();
    $command->fail();

    $response = $this->jsonSentBy(fn () => $command->sendJsonResponse());

    expect($response['success'])->toBeFalse()
        ->and($response['data']['notices'])->toContain('Something went wrong');
});
