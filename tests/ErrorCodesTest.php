<?php

declare(strict_types=1);

use Farzai\Bitkub\Constants\ErrorCodes;

it('should see all error codes', function () {
    expect(ErrorCodes::all())->toHaveCount(47);
});

it('should return message from code success', function () {
    expect(ErrorCodes::getDescription(ErrorCodes::INVALID_USER))
        ->toBe('Invalid user');
});

it('should return unknown error code message for unrecognized code', function () {
    expect(ErrorCodes::getDescription(9999))
        ->toBe('Unknown error code: 9999');
});

it('should return correct descriptions for all known codes', function () {
    expect(ErrorCodes::getDescription(ErrorCodes::NO_ERROR))->toBe('No error');
    expect(ErrorCodes::getDescription(ErrorCodes::SERVER_ERROR))->toBe('Server error (please contact support)');
    expect(ErrorCodes::getDescription(ErrorCodes::INSUFFICIENT_BALANCE))->toBe('Insufficient balance');
});

it('all() includes both constant values and the DESCRIPTIONS array', function () {
    $all = ErrorCodes::all();

    expect($all)->toContain(ErrorCodes::NO_ERROR);
    expect($all)->toContain(ErrorCodes::INVALID_JSON_PAYLOAD);
    expect($all)->toContain(ErrorCodes::SERVER_ERROR);
});
