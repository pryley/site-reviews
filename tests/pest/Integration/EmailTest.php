<?php

use GeminiLabs\SiteReviews\Commands\SendVerificationEmail;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Email;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Notice;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\emptyMailbox;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\sentMail;
use function GeminiLabs\SiteReviews\Tests\sentTo;

/*
 * The email itself: who it says it is from, what it looks like in a client that refuses HTML, and
 * what stops it going out.
 *
 * wp_mail() is short-circuited for the whole suite by bootstrap.php (interceptMail), which records
 * what would have been sent and returns wp_mail()'s success value — so send() takes its production
 * path right up to the transport.
 */

beforeEach(function () {
    resetPluginState();
    glsr(Notice::class)->clear();
});

/**
 * The composed email, sent.
 */
function sendEmail(array $email, array $data = []): bool
{
    return glsr(Email::class)->compose($email, $data)->send();
}

/**
 * @return string[] the headers of the last email
 */
function sentHeaders(): array
{
    $mail = sentMail();

    return (array) (end($mail)['headers'] ?? []);
}

test('an email is addressed, titled and sent', function () {
    $sent = sendEmail([
        'to' => 'someone@example.org',
        'subject' => 'A subject',
        'message' => 'A message.',
    ]);

    expect($sent)->toBeTrue();

    $mail = sentMail();
    expect($mail)->toHaveCount(1);
    expect(sentTo())->toBe(['someone@example.org'])
        ->and($mail[0]['subject'])->toBe('A subject')
        ->and($mail[0]['message'])->toContain('A message.');
});

test('one address or several, given as a string or as a list', function () {
    // `to` is what the callers write and `recipients` is what wp_mail() is given —
    // EmailDefaults maps one to the other (its $mapped property), then sanitizes with
    // `array-string`, which is what turns a comma-separated string into a list.
    sendEmail(['to' => 'one@example.org,two@example.org', 'subject' => 's', 'message' => 'm']);
    expect(sentTo(0))->toBe(['one@example.org', 'two@example.org']);

    sendEmail(['to' => ['three@example.org'], 'subject' => 's', 'message' => 'm']);
    expect(sentTo(1))->toBe(['three@example.org']);
});

test('an email is html, and says so', function () {
    // Without the header the message arrives as a wall of markup.
    sendEmail(['to' => 'a@example.org', 'subject' => 's', 'message' => 'm']);

    expect(sentHeaders())->toContain('Content-Type: text/html');
});

test('an email comes from the site, and can be replied to', function () {
    // The From address is what a spam filter checks against the sending domain, which is
    // why the setting exists and why it is worth asserting that it is used.
    glsr(OptionManager::class)->set('settings.general.notification_from', 'reviews@example.org');

    sendEmail(['to' => 'a@example.org', 'subject' => 's', 'message' => 'm']);

    $headers = sentHeaders();
    $from = sprintf('%s <reviews@example.org>', get_bloginfo('name'));

    expect($headers)->toContain("from: {$from}")
        ->toContain("reply-to: {$from}"); // the reply-to follows the from unless it is given
});

test('an email from a site that has not said falls back to the wordpress admin', function () {
    sendEmail(['to' => 'a@example.org', 'subject' => 's', 'message' => 'm']);

    expect(sentHeaders())->toContain(sprintf('from: %s <%s>',
        get_bloginfo('name'), get_bloginfo('admin_email')
    ));
});

test('an email can be given its own reply-to, cc and bcc', function () {
    sendEmail([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => 'm',
        'reply-to' => 'noreply@example.org',
        'cc' => 'cc@example.org',
        'bcc' => 'bcc@example.org',
    ]);

    expect(sentHeaders())
        ->toContain('reply-to: noreply@example.org')
        ->toContain('cc: cc@example.org')
        ->toContain('bcc: bcc@example.org');
});

/*
 * What stops it.
 */

test('an email with nobody to send it to is not sent, and says which piece is missing', function () {
    // Three things are required and the log names the ones that are absent, because
    // "the email was not sent" on its own is not something anybody can act on.
    expect(sendEmail(['subject' => 's', 'message' => 'm']))->toBeFalse();
    expect(sentMail())->toBeEmpty();
    expect(glsr(Console::class)->get())->toContain('The email is missing the recipient');
});

test('an email with no subject is not sent', function () {
    expect(sendEmail(['to' => 'a@example.org', 'message' => 'm']))->toBeFalse();
    expect(sentMail())->toBeEmpty();

    expect(glsr(Console::class)->get())->toContain('The email is missing the subject');
});

test('an email that wordpress refuses to send is logged with everything needed to work out why', function () {
    // Registered at 20, AFTER the suite's own pre_wp_mail interceptor at 10: that one
    // returns true (which is what wp_mail() returns on success) regardless of what it is
    // handed, so a filter that ran before it would be overruled.
    add_filter('pre_wp_mail', function () {
        do_action('wp_mail_failed', new WP_Error('wp_mail_failed', 'SMTP connect() failed'));

        return false;
    }, 20);

    expect(sendEmail(['to' => 'a@example.org', 'subject' => 's', 'message' => 'm']))->toBeFalse();

    expect(glsr(Console::class)->get())
        ->toContain('[wp_mail] Email was not sent: SMTP connect() failed');
});

/*
 * The plain-text alternative.
 *
 * A mail client set to refuse HTML shows the AltBody, and a message that has been
 * through wp_strip_all_tags() alone is unreadable: the links are gone entirely, the
 * paragraphs are one run-on line, and a bulleted list is a sentence.
 */

test('a link in the plain text keeps the address it pointed at', function () {
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => '<p>Please <a href="https://example.org/verify">verify your review</a>.</p>',
    ]);

    expect($email->read('plaintext'))->toContain('verify your review (https://example.org/verify)');
});

test('a link whose text is its own address is not repeated', function () {
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => '<a href="https://example.org">https://example.org</a>',
    ]);

    expect($email->read('plaintext'))
        ->toContain('https://example.org')
        ->not->toContain('https://example.org (https://example.org)');
});

test('a bulleted list is still a list without any html', function () {
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => '<ul><li>One</li><li>Two</li></ul>',
    ]);

    expect($email->read('plaintext'))->toContain(' - One')->toContain(' - Two');
});

test('the html message is left as html', function () {
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => '<p>Hello.</p>',
    ]);

    expect($email->read())->toContain('<p>Hello.</p>');
});

test('the plain text is attached to the message wordpress is about to send', function () {
    // buildPlainTextMessage() answers `phpmailer_init`, which is the only chance to set
    // AltBody — by then wp_mail() has built the PHPMailer and is about to hand it off.
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => '<p>Hello.</p>',
    ]);
    $phpmailer = new stdClass();
    $phpmailer->AltBody = '';
    $phpmailer->Body = '<p>Hello.</p>';
    $phpmailer->ContentType = 'text/html';

    $email->buildPlainTextMessage($phpmailer);

    expect($phpmailer->AltBody)->toContain('Hello.')->not->toContain('<p>');
});

test('the invisible parts of the message do not become visible in the plain text', function () {
    // The Body at phpmailer_init is whatever WordPress — or another mail plugin, or an
    // addon — has put there, and it has been through no sanitizer of ours. A <style> or
    // a <script> block reaching wp_strip_all_tags() would leave its CSS and its
    // javascript in the message as text, which is what the reader would see.
    //
    // (This cannot be reached through `message`: EmailDefaults sanitizes that with
    // `text-post`, which is wp_kses_post, and kses removes the tags but keeps what is
    // between them — so by then it is already just text.)
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org', 'subject' => 's', 'message' => 'm',
    ]);
    $phpmailer = new stdClass();
    $phpmailer->AltBody = '';
    $phpmailer->Body = '<style>.x{color:red}</style><script>alert(1)</script><p>Hello.</p>';
    $phpmailer->ContentType = 'text/html';

    $email->buildPlainTextMessage($phpmailer);

    expect($phpmailer->AltBody)->toContain('Hello.')
        ->not->toContain('color:red')
        ->not->toContain('alert(1)');
});

test('a plain text alternative that is already there is left alone', function () {
    // An addon, or a mail plugin, may have set one. Overwriting it would throw away
    // whatever it did.
    $email = glsr(Email::class)->compose([
        'to' => 'a@example.org', 'subject' => 's', 'message' => '<p>Hello.</p>',
    ]);
    $phpmailer = new stdClass();
    $phpmailer->AltBody = 'Somebody else wrote this.';
    $phpmailer->Body = '<p>Hello.</p>';
    $phpmailer->ContentType = 'text/html';

    $email->buildPlainTextMessage($phpmailer);

    expect($phpmailer->AltBody)->toBe('Somebody else wrote this.');

    // and a message that is already plain text has no alternative to build
    $phpmailer->AltBody = '';
    $phpmailer->ContentType = 'text/plain';
    $email->buildPlainTextMessage($phpmailer);

    expect($phpmailer->AltBody)->toBe('');
});

/*
 * The body.
 */

test('the message falls back to the notification template the settings hold', function () {
    // This is how the review notification gets its body: the caller passes template
    // tags and no message, and the template in the settings is interpolated with them.
    glsr(OptionManager::class)->set('settings.general.notification_message', 'A review by {review_author}.');

    sendEmail([
        'to' => 'a@example.org',
        'subject' => 's',
        'template-tags' => ['review_author' => 'Jane'],
    ]);

    expect(sentMail()[0]['message'])->toContain('A review by Jane.');
});

test('a shortcode in a review does not run in the email', function () {
    // The message is built from a review somebody on the internet typed. strip_shortcodes()
    // is what stops [site_reviews] — or anything else registered on the site — being
    // executed by the mail template.
    sendEmail([
        'to' => 'a@example.org',
        'subject' => 's',
        'message' => 'Nice plugin [site_reviews] very good',
    ]);

    expect(sentMail()[0]['message'])
        ->toContain('Nice plugin')
        ->not->toContain('[site_reviews]');
});

test('an email is wrapped in the email template', function () {
    sendEmail(['to' => 'a@example.org', 'subject' => 's', 'message' => 'Hello.']);

    expect(sentMail()[0]['message'])->toContain('<html');
});

test('the email is emptied after it is sent', function () {
    // The module is composed and sent, composed and sent — Notification does exactly
    // that for a site notifying several ways. State left behind would go out twice.
    $email = glsr(Email::class);
    $email->compose(['to' => 'a@example.org', 'subject' => 's', 'message' => 'm'])->send();

    expect($email->recipients)->toBe([])
        ->and($email->subject)->toBe('')
        ->and($email->message)->toBe('');
});

/*
 * The verification request, which is the other email the plugin sends.
 *
 * It goes out BY ITSELF: VerificationController::sendVerificationEmail() answers
 * `site-reviews/review/created` (VerificationHooks), so on a site that asks for
 * verification, creating a review sends the email. Which means createReview() sends it
 * too, and a test that then sends one by hand has two in the mailbox — the automatic
 * one first.
 */

function askForVerification(): void
{
    glsr(OptionManager::class)->set('settings.general.request_verification', 'yes');
}

test('a review submitted to a site that asks for verification is emailed a link', function () {
    askForVerification();

    $review = createReview(['email' => 'jane@example.org']);

    $mail = sentMail();
    expect($mail)->toHaveCount(1);
    expect(sentTo())->toBe(['jane@example.org'])
        ->and($mail[0]['subject'])->toBe('Please verify your review');

    // The link is the whole point of the email, and it cannot be compared against
    // $review->verifyUrl() — that builds a NEW token every time it is called, because
    // Encryption::encrypt() uses a random nonce. So the token is taken back out of the
    // email and opened, which is what the router does when somebody clicks it.
    preg_match('/glsr_=([\w-]+)/', (string) $mail[0]['message'], $matches);
    $request = glsr(Encryption::class)->decryptRequest($matches[1] ?? '');

    expect($request['action'])->toBe('verify');
    expect($request['data'][0])->toBe((string) $review->ID);
});

test('a review submitted to a site that does not ask for verification is left alone', function () {
    createReview(['email' => 'jane@example.org']);

    expect(sentMail())->toBeEmpty();
});

test('a verification request can be sent again, with the link it is given', function () {
    // The "Resend Verification Request" button in the review editor. It passes its own
    // URL — the one built from the page the review was submitted on — rather than
    // rebuilding it.
    askForVerification();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createReview(['email' => 'jane@example.org']);
    emptyMailbox(); // the submission above already sent one; this test is about the resend

    $command = new SendVerificationEmail($review, 'https://example.org/verify?token=abc');
    $command->handle();

    expect($command->successful())->toBeTrue();

    $mail = sentMail();
    expect($mail)->toHaveCount(1);
    expect(sentTo())->toBe(['jane@example.org']);
    expect($mail[0]['subject'])->toBe('Please verify your review')
        ->and($mail[0]['message'])->toContain('https://example.org/verify?token=abc');

    // and the review remembers that it was asked, so the button can say "Resend" rather
    // than offering to send it again as though it never had
    expect(glsr(PostMeta::class)->get($review->ID, 'verified_requested'))->toBe('1');
});

test('a verification request with no message template is logged and reported as failed', function () {
    // With the message setting blanked, buildEmail() logs the missing template and composes an email
    // with no body — which Email::validate() then rejects, so send() fails and the command says so
    // rather than reporting a request nobody received.
    askForVerification();
    wp_set_current_user(createUser(['role' => 'administrator']));
    $review = createReview(['email' => 'jane@example.org']); // created while mail still works

    // Blank the template at the option filter (set('') is overwritten by the setting default) so
    // buildEmail logs the missing template; and make wp_mail itself fail so send() returns false.
    add_filter('site-reviews/option/general/request_verification_message', '__return_empty_string');
    remove_all_filters('pre_wp_mail'); // drop the test mail interceptor…
    add_filter('pre_wp_mail', '__return_false'); // …and force the send to fail

    $command = new SendVerificationEmail($review, 'https://example.org/verify?token=abc');
    $command->handle();

    expect($command->successful())->toBeFalse();
});

test('a verification request is not sent by a site that does not ask for verification', function () {
    $review = createReview(['email' => 'jane@example.org']);

    $command = new SendVerificationEmail($review, 'https://example.org/verify');
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(sentMail())->toBeEmpty();
    expect($command->response()['notices'])->toContain('Request Verification setting is disabled');
});

test('a verification request has nowhere to go without an email address', function () {
    askForVerification();
    wp_set_current_user(0);
    $review = createReview(['email' => '']); // and so the automatic one could not go either
    expect($review->email)->toBeEmpty();
    emptyMailbox();
    glsr(Notice::class)->clear();

    $command = new SendVerificationEmail($review, 'https://example.org/verify');
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect(sentMail())->toBeEmpty();
    expect($command->response()['notices'])->toContain('does not have a valid email');
});

test('a verification request for a review that is not there is refused', function () {
    askForVerification();

    $command = new SendVerificationEmail(glsr_get_review(999999001), 'https://example.org/verify');
    $command->handle();

    expect($command->successful())->toBeFalse();
    expect($command->response()['notices'])->toContain('the review is invalid');
});
