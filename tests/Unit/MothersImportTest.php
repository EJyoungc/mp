<?php

use App\Imports\MothersImport;

it('sanitizes phone numbers starting with 265', function () {
    $import = new MothersImport(1);

    expect($import->sanitizePhone('265991234567'))->toBe('0991234567');
    expect($import->sanitizePhone('265881234567'))->toBe('0881234567');
});

it('keeps phone numbers starting with 0 as they are', function () {
    $import = new MothersImport(1);

    expect($import->sanitizePhone('0991234567'))->toBe('0991234567');
    expect($import->sanitizePhone('0881234567'))->toBe('0881234567');
});

it('trims whitespace from phone numbers', function () {
    $import = new MothersImport(1);

    expect($import->sanitizePhone(' 265991234567 '))->toBe('0991234567');
    expect($import->sanitizePhone(' 0991234567 '))->toBe('0991234567');
});

it('handles null or empty phone numbers', function () {
    $import = new MothersImport(1);

    expect($import->sanitizePhone(null))->toBeNull();
    expect($import->sanitizePhone(''))->toBe('');
});
