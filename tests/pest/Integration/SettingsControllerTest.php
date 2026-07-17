<?php

use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Notice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The settings page, on its way into the database.
 *
 * One entry point — sanitizeSettingsCallback(), which WordPress calls from register_setting() —
 * reaches everything else. So the tests call it with the array the form would have posted and assert
 * on what would be written. Three kinds of work happen, easy to confuse:
 *
 *   the MERGE       the form posts one tab, not the whole option. What it did not post must survive.
 *   the RESTORING   an unticked checkbox is not posted at all, so "nothing ticked" and "tab not
 *                   open" look identical in $_POST. Multi-value fields are forced to [] so a person
 *                   CAN untick the last box.
 *   the SANITIZING  every setting runs through the sanitizer named in config/settings.php.
 *
 * One branch is out of reach: the "Settings updated." notice is behind filter_input(INPUT_POST,
 * 'option_page'), always null in a CLI process. Same limitation as the GET routes.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr(Notice::class)->clear(); // a singleton: an error from an earlier test would still be in it
    glsr(Console::class)->clear();
});

/**
 * What the settings form posts: only the tab that was open.
 */
function postedSettings(array $settings): array
{
    return glsr(SettingsController::class)->sanitizeSettingsCallback(['settings' => $settings]);
}

test('the settings are registered with the sanitizer as their callback', function () {
    // The whole of this file hangs off the fact that WordPress calls that method.
    global $wp_registered_settings;

    glsr(SettingsController::class)->registerSettings();

    $registered = $wp_registered_settings[OptionManager::databaseKey()] ?? [];
    [$object, $method] = $registered['sanitize_callback'];

    expect($registered['type'])->toBe('array')
        ->and($registered['group'])->toBe(glsr()->id)
        ->and($object)->toBeInstanceOf(SettingsController::class)
        ->and($method)->toBe('sanitizeSettingsCallback');
});

test('an option that is not the plugin settings is passed straight through', function () {
    // The callback is registered against one option, but WordPress will hand it
    // whatever is in that option — including, on a site being restored or migrated,
    // something that has no `settings` key at all. Rebuilding that into a full
    // settings array would overwrite the real ones with defaults.
    $input = ['something' => 'else'];

    expect(glsr(SettingsController::class)->sanitizeSettingsCallback($input))->toBe($input);
});

test('the settings that were not on the page are kept', function () {
    // The form posts the tab that was open, and nothing else. Every other setting has
    // to come back out of the callback exactly as it went in, or opening the Forms tab
    // and pressing Save would reset the General tab.
    $before = glsr(OptionManager::class)->get('settings.general.style');
    expect($before)->not->toBeEmpty(); // otherwise this asserts nothing

    $options = postedSettings([
        'forms' => ['required' => ['rating']],
    ]);

    expect(Arr::get($options, 'settings.general.style'))->toBe($before);
    expect(Arr::get($options, 'settings.forms.required'))->toBe(['rating']);
});

/*
 * The restoring. An unticked checkbox is not posted, so without these the last box
 * in a group could be ticked but never unticked.
 */

test('a checkbox group that has been emptied is saved as empty', function () {
    glsr(OptionManager::class)->set('settings.forms.required', ['rating', 'title']);

    $options = postedSettings([
        'forms' => [], // every box unticked: the browser posts nothing at all
    ]);

    expect(Arr::get($options, 'settings.forms.required'))->toBe([])
        ->and(Arr::get($options, 'settings.forms.autofill'))->toBe([])
        ->and(Arr::get($options, 'settings.forms.limit_assignments'))->toBe([]);
});

test('the notification recipients can be emptied', function () {
    glsr(OptionManager::class)->set('settings.general.notifications', ['admin']);

    $options = postedSettings([
        'general' => [], // nobody ticked
    ]);

    expect(Arr::get($options, 'settings.general.notifications'))->toBe([]);
});

test('an emptied notification template is restored to the default, not saved blank', function () {
    // The description on the field says so out loud: "To restore the default text,
    // save an empty template." A blank template would send a blank email.
    $default = Arr::get(glsr()->defaults(), 'settings.general.notification_message');
    expect($default)->not->toBeEmpty();

    $options = postedSettings([
        'general' => ['notification_message' => "  \n "],
    ]);

    expect(Arr::get($options, 'settings.general.notification_message'))->toBe($default);
});

test('an emptied verification message is restored to the default', function () {
    $default = Arr::get(glsr()->defaults(), 'settings.general.request_verification_message');
    expect($default)->not->toBeEmpty();

    $options = postedSettings([
        'general' => ['request_verification_message' => ''],
    ]);

    expect(Arr::get($options, 'settings.general.request_verification_message'))->toBe($default);
});

/*
 * The multilingual integration, which is the one setting that can be wrong in a way
 * the plugin can see.
 */

test('a multilingual integration whose plugin is installed is kept', function () {
    // The stubs define POLYLANG_VERSION as 2.3, which is the supported version, so
    // Polylang reads as installed and supported here.
    $options = postedSettings([
        'general' => ['multilingual' => 'polylang'],
    ]);

    expect(Arr::get($options, 'settings.general.multilingual'))->toBe('polylang');
    expect(glsr(Notice::class)->get())->toBe('');
});

test('a multilingual integration whose plugin is not installed is refused, and said so', function () {
    // Saving it would leave the setting claiming an integration that cannot run, and
    // reviews would silently stop being assigned across languages.
    $options = postedSettings([
        'general' => ['multilingual' => 'wpml'],
    ]);

    expect(Arr::get($options, 'settings.general.multilingual'))->toBe('');
    expect(glsr(Notice::class)->get())->toContain('install/activate the WPML plugin');
});

test('a multilingual integration nobody has ever heard of is refused', function () {
    $options = postedSettings([
        'general' => ['multilingual' => 'esperanto'],
    ]);

    expect(Arr::get($options, 'settings.general.multilingual'))->toBe('');
    expect(glsr(Console::class)->get())->toContain('Multilingual\Esperanto does not exist');
});

/*
 * The custom text.
 *
 * Every string a visitor sees can be overridden here, which makes this the one place
 * on the settings page where somebody types HTML on purpose.
 */

test('custom text keeps the tags it is allowed and loses the rest', function () {
    $options = postedSettings([
        'strings' => [
            ['s1' => 'Read more', 's2' => '<a class="x" href="/more" target="_blank">Read more</a><script>alert(1)</script>'],
        ],
    ]);

    $string = Arr::get($options, 'settings.strings.0.s2');
    expect($string)->toContain('<a class="x" href="/more" target="_blank">')
        ->not->toContain('<script'); // wp_kses, with a list of two tags
});

test('a custom string that has lost its placeholder is an error, not a saved string', function () {
    // "%s reviews" with the %s taken out prints the word "reviews" and no number, and
    // nobody would guess why. The string is still saved — it is the person's site —
    // but they are told, and told which one.
    $options = postedSettings([
        'strings' => [
            ['s1' => '%s star', 'p1' => '%s stars', 's2' => 'star', 'p2' => '%s stars'],
        ],
    ]);

    expect(glsr(Notice::class)->get())
        ->toContain('forgot to include the <code>%s</code> placeholder')
        ->toContain('star'); // the offending translation is shown as a detail
    expect(Arr::get($options, 'settings.strings.0.s2'))->toBe('star');
});

test('a plural placeholder is checked as well as a singular one', function () {
    $options = postedSettings([
        'strings' => [
            ['s1' => '%d review', 'p1' => '%d reviews', 's2' => '%d review', 'p2' => 'reviews'],
        ],
    ]);

    expect(glsr(Notice::class)->get())->toContain('<code>%d</code>');
});

test('a string with no original is dropped', function () {
    // Every entry the settings page renders has an `s1`. One without is a row the
    // browser (or something worse) has invented.
    $options = postedSettings([
        'strings' => [
            ['s2' => 'a translation of nothing'],
            ['s1' => 'Read more', 's2' => 'Read on'],
        ],
    ]);

    $strings = Arr::get($options, 'settings.strings');
    expect($strings)->toHaveCount(1);
    expect($strings[0]['s1'])->toBe('Read more');
});

/*
 * The sanitizers, which are named per setting in config/settings.php.
 */

test('every setting is put through the sanitizer its own config names', function () {
    $options = postedSettings([
        'general' => [
            'notification_email' => 'someone@example.org, not-an-email, other@example.org',
            'notifications' => ['admin', 'custom'],
        ],
    ]);

    // `emails`: each address is sanitized and the ones that are not addresses go
    expect(Arr::get($options, 'settings.general.notification_email'))
        ->toBe('someone@example.org,other@example.org');
    expect(Arr::get($options, 'settings.general.notifications'))->toBe(['admin', 'custom']);
});

test('a webhook url has to be the webhook of the service it claims to be', function () {
    // `url:discord.com`. Without the host check, the notification for every new review
    // — the reviewer's name, email and words — would be POSTed to whatever host was
    // typed into the box.
    $options = postedSettings([
        'general' => [
            'notification_discord' => 'https://discord.com/api/webhooks/123/abc',
            'notification_slack' => 'https://evil.example.com/collect',
        ],
    ]);

    expect(Arr::get($options, 'settings.general.notification_discord'))
        ->toBe('https://discord.com/api/webhooks/123/abc');
    expect(Arr::get($options, 'settings.general.notification_slack'))->toBe('');
});

test('what was saved is announced', function () {
    $announced = new ArrayObject();
    add_action('site-reviews/settings/updated', fn ($options, $input) => $announced->append($options), 10, 2);

    postedSettings(['forms' => ['required' => ['rating']]]);

    expect($announced)->toHaveCount(1);
    expect(Arr::get($announced[0], 'settings.forms.required'))->toBe(['rating']);
});

test('saving from the settings screen announces success', function () {
    // the option_page marker is what separates the settings screen submitting
    // from anything else writing the option
    $_POST['option_page'] = glsr()->id;
    try {
        postedSettings(['general' => ['require' => ['approval' => 'no']]]);

        expect(glsr(Notice::class)->get())->toContain('Settings updated');
    } finally {
        unset($_POST['option_page']);
    }
});

test('a multilingual integration that is installed but too old is refused with the version named', function () {
    // Polylang is installed (the stubs) at exactly its supported version; a fake
    // that demands more stands in for a site running an outdated copy.
    $tooOld = new class extends \GeminiLabs\SiteReviews\Modules\Multilingual\Polylang {
        public $supportedVersion = '99.0';
    };
    $original = glsr(\GeminiLabs\SiteReviews\Modules\Multilingual\Polylang::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Modules\Multilingual\Polylang::class, $tooOld);
    try {
        $options = postedSettings(['general' => ['multilingual' => 'polylang']]);

        expect(Arr::get($options, 'settings.general.multilingual'))->toBe('');
        expect(glsr(Notice::class)->get())->toContain('update the Polylang plugin')
            ->and(glsr(Notice::class)->get())->toContain('99.0');
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Modules\Multilingual\Polylang::class, $original);
    }
});
