<?php

use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Upload;
use GeminiLabs\SiteReviews\UploadedFile;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The uploaded file, and the trait that fetches it out of $_FILES.
 *
 * The front door for the settings import and the CSV import — the one place the plugin takes a file
 * from a stranger, so what it does with a file it does not like matters more than one it does.
 *
 * ONE BRANCH CANNOT BE REACHED HERE: isValid() ends in is_uploaded_file(), which PHP returns true
 * for only a file that arrived in THIS request over HTTP POST. A CLI process has none and cannot
 * make one, so isValid() is always false here — precisely the security property it exists for — and
 * every caller's happy path beyond it (Upload::getImportFile() returning a file, ImportSettings
 * reading it) is out of reach. ImportSettings is covered from import() inwards instead; see
 * ExportImportTest.
 */

beforeEach(function () {
    resetPluginState();
    glsr(Notice::class)->clear();
});

afterEach(function () {
    $_FILES = [];
    glsr(Notice::class)->clear();
});

/**
 * A file on disk, and the $_FILES entry PHP would have built for it.
 */
function uploadedFileData(string $contents, string $name = 'settings.json', string $type = 'application/json'): array
{
    $path = tempnam(sys_get_temp_dir(), 'glsr');
    file_put_contents($path, $contents);

    return [
        'error' => \UPLOAD_ERR_OK,
        'name' => $name,
        'size' => strlen($contents),
        'tmp_name' => $path,
        'type' => $type,
    ];
}

/**
 * The Upload trait's protected methods, reachable.
 */
class UploadHarness
{
    use Upload;

    public function callFile(): UploadedFile
    {
        return $this->file();
    }

    public function callFiles(): array
    {
        return $this->files();
    }

    public function callFixPhpFilesArray(array $data): array
    {
        return $this->fixPhpFilesArray($data);
    }

    public function callGetImportFile(string $mimeType): ?UploadedFile
    {
        return $this->getImportFile($mimeType);
    }

    public function callGetImportFileData(UploadedFile $file): array
    {
        return $this->getImportFileData($file);
    }
}

test('a file that did not arrive over http is never valid', function () {
    // The whole point of is_uploaded_file(): a path in $_FILES is a claim, and a
    // caller that trusted it could be handed /etc/passwd. Nothing this test process
    // can write to disk will ever satisfy it.
    $file = new UploadedFile(uploadedFileData('{}'));

    expect($file->isValid())->toBeFalse()
        ->and($file->getError())->toBe(\UPLOAD_ERR_OK);
});

test('a file whose temporary copy is gone is refused at construction', function () {
    $data = uploadedFileData('{}');
    unlink($data['tmp_name']);

    expect(fn () => new UploadedFile($data))->toThrow(FileNotFoundException::class);
});

test('a failed upload is not looked for on disk', function () {
    // No tmp file is written when the upload failed, so the constructor must not go
    // hunting for one — the error code is the whole story.
    $file = new UploadedFile([
        'error' => \UPLOAD_ERR_NO_FILE,
        'name' => '',
        'size' => 0,
        'tmp_name' => '',
        'type' => '',
    ]);

    expect($file->getError())->toBe(\UPLOAD_ERR_NO_FILE)
        ->and($file->getErrorMessage())->toBe('No file was uploaded.')
        ->and($file->isValid())->toBeFalse();
});

test('every upload error has something to say for itself', function (int $error, string $expected) {
    $file = new UploadedFile([
        'error' => $error,
        'name' => 'settings.json',
        'size' => 0,
        'tmp_name' => '',
        'type' => '',
    ]);

    expect($file->getErrorMessage())->toContain($expected);
})->with([
    'partial' => [\UPLOAD_ERR_PARTIAL, 'was only partially uploaded'],
    'no tmp dir' => [\UPLOAD_ERR_NO_TMP_DIR, 'missing temporary directory'],
    'cant write' => [\UPLOAD_ERR_CANT_WRITE, 'could not be written on disk'],
    'extension' => [\UPLOAD_ERR_EXTENSION, 'stopped by a PHP extension'],
    'form size' => [\UPLOAD_ERR_FORM_SIZE, 'exceeds the upload limit'],
    'ini size' => [\UPLOAD_ERR_INI_SIZE, 'exceeds the upload_max_filesize'],
]);

test('an upload that succeeded has nothing to say for itself', function () {
    expect((new UploadedFile(uploadedFileData('{}')))->getErrorMessage())->toBe('');
});

test('the client name is stripped of any path it arrived with', function () {
    // The name comes from the browser and is not to be trusted as a path.
    $file = new UploadedFile(uploadedFileData('{}', 'C:\\Users\\someone\\settings.json'));

    expect($file->getClientOriginalName())->toBe('settings.json')
        ->and($file->getClientOriginalExtension())->toBe('json');
});

test('the mime type is read from the file, not from what the browser claimed', function () {
    // "This should not be considered a safe value" — so the plugin looks at the
    // bytes. A JSON file announced as an image is still JSON.
    $file = new UploadedFile(uploadedFileData('{"a":1}', 'settings.json', 'image/png'));

    expect($file->getClientMimeType())->toBe('image/png') // what was claimed
        ->and($file->getMimeType())->not->toBe('image/png'); // what it actually is
});

test('a json file is accepted as json', function () {
    // finfo reports a small JSON file as text/plain or application/json depending on
    // which libmagic it was built against, and both are accepted: text/plain is on
    // the inconclusive list. Either verdict passes, so this one does not depend on
    // which is given.
    $file = new UploadedFile(uploadedFileData('{"settings":{"general":{}}}'));

    expect($file->hasMimeType('application/json'))->toBeTrue();
});

/**
 * A file whose detected mime type the test decides.
 *
 * hasMimeType() branches on what getMimeType() found in the bytes, and what finfo
 * finds there is a property of the libmagic the container was built with — comma
 * separated text is 'text/csv' to some versions and 'text/plain' to others. Pinning
 * the branches against a live finfo would be pinning the container. This overrides
 * the one method whose answer comes from outside, and nothing else.
 */
class FileDetectedAs extends UploadedFile
{
    public string $detected = '';

    public function getMimeType(): string
    {
        return $this->detected;
    }
}

function fileDetectedAs(string $detected, string $name): FileDetectedAs
{
    $file = new FileDetectedAs(uploadedFileData("a,b\n1,2\n", $name));
    $file->detected = $detected;

    return $file;
}

test('a csv is accepted only if its name agrees with its contents', function (string $detected) {
    // A CSV cannot be told from plain text by its bytes, so when the content looks
    // like one the extension has to agree — a .txt full of commas is not a CSV
    // import, whichever of the three CSV types finfo decided on.
    expect(fileDetectedAs($detected, 'reviews.csv')->hasMimeType('text/csv'))->toBeTrue();
    expect(fileDetectedAs($detected, 'reviews.txt')->hasMimeType('text/csv'))->toBeFalse();
})->with([
    'text/csv',
    'application/csv',
    'application/vnd.ms-excel',
]);

test('a mime type that tells us nothing is not held against the file', function (string $detected) {
    // An empty file, or one libmagic will not commit on, is let through — refusing
    // it would refuse a perfectly good import on the strength of a shrug.
    expect(fileDetectedAs($detected, 'settings.json')->hasMimeType('application/json'))->toBeTrue();
})->with([
    'application/octet-stream',
    'application/x-empty',
    'text/plain',
]);

test('a file that is something else entirely is refused', function () {
    expect(fileDetectedAs('image/png', 'settings.json')->hasMimeType('application/json'))->toBeFalse();
});

test('the contents can be read whole or in chunks', function () {
    $json = '{"settings":{"general":{"require":{"approval":"no"}}}}';
    $file = new UploadedFile(uploadedFileData($json));

    expect($file->getContent())->toBe($json);

    $chunks = iterator_to_array($file->streamContent(8));
    expect(count($chunks))->toBeGreaterThan(1)      // it really did chunk
        ->and(implode('', $chunks))->toBe($json);   // and lost nothing doing it
});

test('the import file is taken from $_FILES', function () {
    $_FILES['import-files'] = uploadedFileData('{}');

    expect((new UploadHarness())->callFile()->getClientOriginalName())->toBe('settings.json');
});

test('a multi-file upload is untangled into one entry per file', function () {
    // PHP does not give you a list of files; it gives you a file of lists, with the
    // fields transposed. fixPhpFilesArray puts them back the right way round.
    $first = uploadedFileData('{"a":1}', 'one.json');
    $second = uploadedFileData('{"b":2}', 'two.json');
    $_FILES['import-files'] = [
        'error' => [$first['error'], $second['error']],
        'name' => [$first['name'], $second['name']],
        'size' => [$first['size'], $second['size']],
        'tmp_name' => [$first['tmp_name'], $second['tmp_name']],
        'type' => [$first['type'], $second['type']],
    ];

    $files = (new UploadHarness())->callFiles();

    expect($files)->toHaveCount(2)
        ->and($files[0]->getClientOriginalName())->toBe('one.json')
        ->and($files[1]->getClientOriginalName())->toBe('two.json');

    // file() takes the first of them
    expect((new UploadHarness())->callFile()->getClientOriginalName())->toBe('one.json');
});

test('a file list with a missing file is skipped rather than fatal', function () {
    $present = uploadedFileData('{"a":1}', 'one.json');
    $missing = uploadedFileData('{"b":2}', 'two.json');
    unlink($missing['tmp_name']);
    $_FILES['import-files'] = [
        'error' => [$present['error'], $missing['error']],
        'name' => [$present['name'], $missing['name']],
        'size' => [$present['size'], $missing['size']],
        'tmp_name' => [$present['tmp_name'], $missing['tmp_name']],
        'type' => [$present['type'], $missing['type']],
    ];

    $files = (new UploadHarness())->callFiles();

    expect($files)->toHaveCount(1)
        ->and($files[0]->getClientOriginalName())->toBe('one.json');
});

test('a single-file upload is wrapped into a list of one', function () {
    // When only one file is uploaded $_FILES is flat, not the transposed array of a multi-file
    // upload, so files() wraps it into a one-item list rather than iterating its characters.
    $_FILES['import-files'] = uploadedFileData('{"a":1}', 'only.json');

    $files = (new UploadHarness())->callFiles();

    expect($files)->toHaveCount(1)
        ->and($files[0]->getClientOriginalName())->toBe('only.json');
});

test('the full_path key php 8.1 adds is dropped', function () {
    // PHP 8.1 added full_path to $_FILES, which broke the shape fixPhpFilesArray
    // matches on: it compares the key list against exactly five names.
    $data = uploadedFileData('{}');
    $data['full_path'] = 'some/where/settings.json';

    $fixed = (new UploadHarness())->callFixPhpFilesArray($data);

    expect($fixed)->not->toHaveKey('full_path')
        ->and($fixed['name'])->toBe('settings.json');
});

test('no file to import is reported to the user', function () {
    // The form was submitted with nothing chosen. UploadedFileDefaults defaults the
    // error code to -1 rather than to UPLOAD_ERR_OK, so this does NOT go looking for
    // a temp file and throw — it builds a file whose error is a code PHP never
    // issues, which isValid() rejects and getErrorMessage() has no name for.
    $_FILES['import-files'] = [];

    expect((new UploadHarness())->callGetImportFile('application/json'))->toBeNull();
    expect(glsr(Notice::class)->get())
        ->toContain('notice-error')
        ->toContain('unknown error');
});

test('an import file whose temporary copy has vanished is refused, not fatal', function () {
    // The other way in: a file that WAS uploaded (error OK) but whose temp copy is already gone by
    // the time getImportFile() looks. file() throws FileNotFoundException; getImportFile catches it,
    // reports it, and returns null instead of letting it escape.
    $data = uploadedFileData('{}');
    unlink($data['tmp_name']);
    $_FILES['import-files'] = $data;

    expect((new UploadHarness())->callGetImportFile('application/json'))->toBeNull();
    expect(glsr(Notice::class)->get())->toContain('notice-error');
});

/**
 * A file that claims to have arrived over HTTP.
 *
 * Same seam as FileDetectedAs above: isValid() ends in is_uploaded_file(), whose
 * answer comes from the SAPI and is unfakeable here (see the file header). This
 * overrides that one answer so the trait's post-validation branches — the mime
 * refusal and the success return — can run; everything else is real.
 */
class FileClaimingValid extends UploadedFile
{
    public function isValid(): bool
    {
        return \UPLOAD_ERR_OK === $this->getError();
    }
}

test('an import file of the wrong kind is refused with its real type named', function () {
    // A GIF renamed settings.json: the bytes say image/gif, conclusively (probed —
    // a truncated PNG reads as octet-stream, which is inconclusive and accepted),
    // and the refusal names GIF so the person knows what they actually uploaded.
    $png = uploadedFileData('GIF89a'.str_repeat("\x00", 20), 'settings.json');
    $harness = new class extends UploadHarness {
        protected function file(): UploadedFile
        {
            return new FileClaimingValid($GLOBALS['glsr_test_filedata']);
        }
    };
    $GLOBALS['glsr_test_filedata'] = $png;

    try {
        expect($harness->callGetImportFile('application/json'))->toBeNull();
        expect(glsr(Notice::class)->get())->toContain('not a valid GIF file');

        // and the right kind passes through as the file itself
        glsr(Notice::class)->clear();
        $GLOBALS['glsr_test_filedata'] = uploadedFileData('{"settings":{}}');
        $file = $harness->callGetImportFile('application/json');
        expect($file)->toBeInstanceOf(UploadedFile::class)
            ->and(glsr(Notice::class)->get())->toBe('');
    } finally {
        unset($GLOBALS['glsr_test_filedata']);
    }
});

test('a file that cannot be streamed is reported, not fatal', function () {
    // getContent() throws FileException when a read fails mid-stream;
    // getImportFileData turns that into a notice and an empty import.
    $file = new class(uploadedFileData('{}')) extends UploadedFile {
        public function getContent(): string
        {
            throw new \GeminiLabs\SiteReviews\Exceptions\FileException('Could not stream the contents of "settings.json".');
        }
    };

    expect((new UploadHarness())->callGetImportFileData($file))->toBe([]);
    expect(glsr(Notice::class)->get())->toContain('Could not stream');
});

test('the client size is what the request claimed', function () {
    expect((new UploadedFile(uploadedFileData('{"a":1}')))->getClientSize())->toBe(7);
});

test('the extension is derived from the detected mime type, with the client name as last resort', function () {
    // application/json is not in WordPress's allowed mime types; the plugin adds it.
    expect(fileDetectedAs('application/json', 'whatever.bin')->getExtensionFromMimeType())->toBe('json');

    // a mime type nothing maps to: the temp file has no extension, so the client name answers
    expect(fileDetectedAs('application/x-bogus', 'photo.png')->getExtensionFromMimeType())->toBe('png');
});

test('when the file cannot be inspected the client mime type is the fallback', function () {
    // error != OK means no temp file exists, so finfo and mime_content_type both
    // fail (probed: false + E_WARNING each) and the claimed type is all that is left.
    $file = new UploadedFile([
        'error' => \UPLOAD_ERR_PARTIAL,
        'name' => 'settings.json',
        'size' => 0,
        'tmp_name' => '/nonexistent/glsr-test-xyz',
        'type' => 'application/json',
    ]);

    set_error_handler(fn () => true); // the warnings are the branch's precondition, not a defect
    try {
        expect($file->getMimeType())->toBe('application/json');
    } finally {
        restore_error_handler();
    }
});

test('json is decoded, and an empty file says so', function () {
    $harness = new UploadHarness();
    $file = new UploadedFile(uploadedFileData('{"settings":{"general":{"require":{"approval":"no"}}}}'));

    expect($harness->callGetImportFileData($file))
        ->toBe(['settings' => ['general' => ['require' => ['approval' => 'no']]]]);

    glsr(Notice::class)->clear();
    $empty = new UploadedFile(uploadedFileData(''));
    expect($harness->callGetImportFileData($empty))->toBe([]);
    expect(glsr(Notice::class)->get())->toContain('There was nothing found to import');
});
