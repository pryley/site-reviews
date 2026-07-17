<?php

use GeminiLabs\SiteReviews\Modules\Notice;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Modules\Notice, the flash-message queue behind every admin action's feedback
 * (distinct from Notices\*, the persistent admin banners). The transient is how
 * a notice survives the redirect after a POST.
 */

beforeEach(function () {
    resetPluginState();
    glsr(Notice::class)->clear();
});

afterEach(function () {
    glsr(Notice::class)->clear();
    delete_transient(glsr()->prefix.'notices');
});

test('a stored notice survives into the next request, once', function () {
    glsr(Notice::class)->addSuccess('Settings saved.')->store();
    expect(get_transient(glsr()->prefix.'notices'))->not->toBeFalse();

    // "the next request": a fresh Notice picks the transient up and burns it
    $nextRequest = new Notice();
    expect($nextRequest->get())->toContain('Settings saved.')
        ->and(get_transient(glsr()->prefix.'notices'))->toBeFalse();

    // and the request after that starts clean
    expect((new Notice())->get())->toBe('');
});

test('a WP_Error can be handed over directly as the message', function () {
    glsr(Notice::class)->addError(new WP_Error('code', 'The thing failed spectacularly.'));

    expect(glsr(Notice::class)->get())->toContain('The thing failed spectacularly.')
        ->and(glsr(Notice::class)->get())->toContain('notice-error');
});
