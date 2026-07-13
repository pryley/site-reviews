<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Discord;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Modules\Slack;

use function GeminiLabs\SiteReviews\Tests\createPost;
use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\sentJson;
use function GeminiLabs\SiteReviews\Tests\sentMail;
use function GeminiLabs\SiteReviews\Tests\sentTo;

/*
 * What goes out when a review comes in.
 *
 * Five recipients are possible and they are independent: the administrator, the author
 * of the page the review is about, a list of addresses somebody typed in, a Discord
 * channel and a Slack channel. Which of them are used is one setting,
 * general.notifications, and everything below turns on it.
 *
 * The webhooks POST the review — the reviewer's name, their email address, their IP —
 * to a third party. Two things follow. One: nothing here may be allowed to reach the
 * network, which bootstrap.php now enforces for the whole suite (blockHttpRequests()).
 * Two: what is IN the payload is worth asserting, because it is somebody's personal
 * data leaving the site.
 *
 * Slack and Discord are constructed with their webhook read from the settings, so the
 * setting has to be written before the module is resolved. The container does not cache
 * these, so a fresh glsr(Slack::class) picks up whatever was just set.
 *
 * And note the ORDER of every test below: the review is created BEFORE interceptHttp()
 * is armed. Creating a review makes an HTTP request of its own — Avatar::generate()
 * asks secure.gravatar.com whether the reviewer has a gravatar (isUrlOnline() ->
 * Helper::remoteStatusCheck()) — and an interceptor armed first would record it as
 * though the plugin had sent a notification, and answer 200, which would also change
 * the avatar the review ends up with. Armed second, that request falls through to
 * blockHttpRequests() and comes back as a WP_Error, which is what it does everywhere
 * else in the suite: no gravatar, use the fallback.
 */

beforeEach(function () {
    resetPluginState();
    glsr(Console::class)->clear();
});

function notifyBy(array $types): void
{
    glsr(OptionManager::class)->set('settings.general.notifications', $types);
}

/*
 * Who is told.
 */

test('a site that has asked for no notifications gets none', function () {
    $review = createReview();
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect(sentMail())->toBeEmpty();
    expect($http)->toHaveCount(0);
});

test('the administrator is told, at the address wordpress has for them', function () {
    notifyBy(['admin']);

    glsr(Notification::class)->send(createReview());

    expect(sentMail())->toHaveCount(1);
    expect(sentTo())->toBe([get_option('admin_email')]);
});

test('the author of the page a review is about is told', function () {
    // The point of the `author` recipient: on a multi-author site the person who wrote
    // the page is the person who wants to know it has been reviewed, and they are not
    // necessarily the administrator.
    $author = createUser(['user_email' => 'author@example.org', 'role' => 'author']);
    $postId = createPost(['post_author' => $author]);
    notifyBy(['author']);

    glsr(Notification::class)->send(createReview(['assigned_posts' => $postId]));

    expect(sentTo())->toBe(['author@example.org']);
});

test('a review about nothing in particular has no author to tell', function () {
    notifyBy(['author']);

    glsr(Notification::class)->send(createReview()); // assigned to no page

    expect(sentMail())->toBeEmpty(); // no recipient, so Email::validate() refuses it
    expect(glsr(Console::class)->get())->toContain('missing the recipient');
});

test('the addresses somebody typed in are told, however they separated them', function () {
    // The field says "separate multiple emails with a comma", and people use semicolons
    // and spaces anyway. All three are accepted rather than silently producing one
    // long invalid address.
    notifyBy(['custom']);
    glsr(OptionManager::class)->set(
        'settings.general.notification_email',
        'one@example.org, two@example.org;three@example.org four@example.org'
    );

    glsr(Notification::class)->send(createReview());

    expect(sentTo())->toBe([
        'one@example.org', 'two@example.org', 'three@example.org', 'four@example.org',
    ]);
});

test('nobody is told twice', function () {
    // The administrator's address typed into the custom list as well is one email, not
    // two.
    notifyBy(['admin', 'custom']);
    glsr(OptionManager::class)->set('settings.general.notification_email', get_option('admin_email'));

    glsr(Notification::class)->send(createReview());

    expect(sentTo())->toBe([get_option('admin_email')]);
});

test('the recipients can be changed by a filter', function () {
    notifyBy(['admin']);
    add_filter('site-reviews/notification/emails', fn () => ['someone@example.org']);

    glsr(Notification::class)->send(createReview());

    expect(sentTo())->toBe(['someone@example.org']);
});

/*
 * What it is called.
 */

test('the subject says the rating, and the site it came from', function () {
    notifyBy(['admin']);

    glsr(Notification::class)->send(createReview(['rating' => 4]));

    expect(sentMail()[0]['subject'])
        ->toContain('New 4-star review')
        ->toContain(get_option('blogname'));
});

test('the subject says what the review is of, when it is of something', function () {
    notifyBy(['admin']);
    $postId = createPost(['post_title' => 'The Widget']);

    glsr(Notification::class)->send(createReview(['rating' => 5, 'assigned_posts' => $postId]));

    expect(sentMail()[0]['subject'])->toContain('New 5-star review of The Widget');
});

test('the subject can be changed by a filter', function () {
    notifyBy(['admin']);
    add_filter('site-reviews/notification/title', fn () => 'Something happened');

    glsr(Notification::class)->send(createReview());

    expect(sentMail()[0]['subject'])->toBe('Something happened');
});

/*
 * Discord.
 */

function discordWebhook(): string
{
    $webhook = 'https://discord.com/api/webhooks/1/abc';
    glsr(OptionManager::class)->set('settings.general.notification_discord', $webhook);

    return $webhook;
}

test('discord is not sent to without a webhook to send to, and the console says so', function () {
    // The notification type can be ticked without the URL ever being pasted in. Silence
    // would leave somebody waiting for a message that is never coming.
    notifyBy(['discord']);
    $review = createReview();
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect($http)->toHaveCount(0);
    expect(glsr(Console::class)->get())->toContain('Discord notification was not sent: missing webhook');
});

test('a discord notification carries the review, and where to act on it', function () {
    notifyBy(['discord']);
    $webhook = discordWebhook();
    $review = createReview([
        'content' => 'It worked.',
        'email' => 'jane@example.org',
        'is_approved' => false,
        'ip_address' => '127.0.0.1',
        'name' => 'Jane',
        'rating' => 4,
        'title' => 'Very good',
    ]);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect($http)->toHaveCount(1);
    expect($http[0]['url'])->toBe($webhook);
    expect($http[0]['args']['headers']['Content-Type'])->toBe('application/json');

    $payload = sentJson($http);
    $embed = $payload['embeds'][0];

    expect($payload['content'])->toContain('New 4-star review');
    expect($embed['title'])->toBe('Very good');
    expect($embed['description'])->toContain('★★★★☆')->toContain('It worked.');
    expect($embed['color'])->toBeInt(); // DiscordDefaults turns the hex into the integer Discord wants

    // the reviewer's name, email address and IP go to Discord. That is the feature, and
    // it is also the reason to assert on the payload at all.
    $fields = array_column($embed['fields'], 'value', 'name');
    expect($fields['Name'])->toBe('Jane')
        ->and($fields['Email'])->toBe('jane@example.org')
        ->and($fields['IP Address'])->toBe($review->ip_address);

    // the moderation links are the reason to send it at all: an unapproved review can
    // be approved from the Discord message
    $links = end($embed['fields'])['value'];
    expect($links)->toContain('Approve Review')->toContain('Edit Review');
});

test('an approved review is not offered for approval again', function () {
    notifyBy(['discord']);
    discordWebhook();
    $review = createReview(['is_approved' => true]);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    $fields = sentJson($http)['embeds'][0]['fields'];
    $links = end($fields)['value'];

    expect($links)->not->toContain('Approve Review')
        ->and($links)->toContain('Edit Review');
});

test('a review too long for discord is cut short rather than rejected', function () {
    // Discord refuses an embed description over 2000 characters outright, so a long
    // review would mean no notification at all instead of a truncated one.
    notifyBy(['discord']);
    discordWebhook();
    $review = createReview(['content' => str_repeat('a', 3000)]);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    $description = sentJson($http)['embeds'][0]['description'];

    expect(mb_strlen($description))->toBeLessThanOrEqual(2000); // 1999 characters, and the ellipsis
    expect($description)->toEndWith('…');
});

test('a review with no title still has a title in discord', function () {
    notifyBy(['discord']);
    discordWebhook();
    $review = createReview(['title' => '']);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect(sentJson($http)['embeds'][0]['title'])->toBe('(no title)');
});

/*
 * Slack.
 */

function slackWebhook(): string
{
    $webhook = 'https://hooks.slack.com/services/T/B/x';
    glsr(OptionManager::class)->set('settings.general.notification_slack', $webhook);

    return $webhook;
}

test('slack is not sent to without a webhook to send to, and the console says so', function () {
    notifyBy(['slack']);
    $review = createReview();
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect($http)->toHaveCount(0);
    expect(glsr(Console::class)->get())->toContain('Slack notification was not sent: missing webhook');
});

test('a slack notification carries the review, and where to act on it', function () {
    notifyBy(['slack']);
    $webhook = slackWebhook();
    $review = createReview([
        'content' => 'It worked.',
        'email' => 'jane@example.org',
        'is_approved' => false,
        'ip_address' => '127.0.0.1',
        'name' => 'Jane',
        'rating' => 4,
        'title' => 'Very good',
    ]);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect($http[0]['url'])->toBe($webhook);

    $blocks = sentJson($http)['blocks'];
    $fields = wp_json_encode($blocks[3]['fields'], JSON_UNESCAPED_UNICODE);

    expect(array_column($blocks, 'type'))->toBe(['header', 'section', 'section', 'section', 'actions']);
    expect($blocks[0]['text']['text'])->toContain('New 4-star review');
    expect($blocks[1]['text']['text'])->toContain('*Very good*')->toContain('★★★★☆');
    expect($blocks[2]['text']['text'])->toBe('It worked.');
    expect($fields)->toContain('*Name:* Jane')
        ->toContain('*Email:* jane@example.org')
        ->toContain('*IP Address:* '.$review->ip_address);
    expect(array_column($blocks[4]['elements'], 'url'))->toHaveCount(2); // approve and edit
});

test('a slack notification leaves out the blocks it has nothing to put in', function () {
    // Slack rejects a section with an empty text field, so a review with no content
    // must not produce an empty content section — the block is dropped instead.
    notifyBy(['slack']);
    slackWebhook();
    $review = createReview(['content' => '  ', 'is_approved' => true]);
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    $blocks = sentJson($http)['blocks'];

    // What is left is the header, the title, the fields — which is also a `section` —
    // and the actions. The content section is the one that is gone.
    expect(array_column($blocks, 'type'))->toBe(['header', 'section', 'section', 'actions']);
    expect($blocks[2])->toHaveKey('fields');
    expect($blocks[3]['elements'])->toHaveCount(1); // and only the edit button
});

test('a slack notification can be rewritten wholesale by a filter', function () {
    notifyBy(['slack']);
    slackWebhook();
    add_filter('site-reviews/slack/notification', fn () => ['text' => 'Replaced.']);
    $review = createReview();
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect(sentJson($http))->toBe(['text' => 'Replaced.']);
});

test('a webhook that refuses the notification is logged, not thrown', function () {
    // A dead webhook must not take the review submission down with it — the visitor has
    // already been told their review was received.
    slackWebhook();
    $review = createReview();
    add_filter('pre_http_request', fn () => new WP_Error('http_request_failed', 'Could not resolve host'), 10);

    $sent = glsr(Slack::class)->compose($review, ['header' => 'x'])->send();

    expect($sent)->toBeFalse();
    expect(glsr(Console::class)->get())->toContain('Could not resolve host');
});

/*
 * Both webhooks are sent when both are asked for.
 */

test('a site can tell discord and slack and everybody else at once', function () {
    notifyBy(['admin', 'discord', 'slack']);
    discordWebhook();
    slackWebhook();
    $review = createReview();
    $http = interceptHttp();

    glsr(Notification::class)->send($review);

    expect(sentMail())->toHaveCount(1);
    expect(array_column($http->getArrayCopy(), 'url'))->toBe([
        'https://discord.com/api/webhooks/1/abc',
        'https://hooks.slack.com/services/T/B/x',
    ]);
});

test('the webhooks are composed for the review they are about, and not the one before', function () {
    // Discord and Slack are stateful — compose() sets $this->review and send() reads it.
    // Two reviews notified in the same request must not both go out as the first one.
    discordWebhook();
    $one = createReview(['title' => 'The first']);
    $two = createReview(['title' => 'The second']);
    $http = interceptHttp();

    glsr(Discord::class)->compose($one, ['header' => 'a'])->send();
    glsr(Discord::class)->compose($two, ['header' => 'b'])->send();

    expect(sentJson($http, 0)['embeds'][0]['title'])->toBe('The first');
    expect(sentJson($http, 1)['embeds'][0]['title'])->toBe('The second');
});
