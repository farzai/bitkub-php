<?php

use Farzai\Bitkub\Constants\ErrorCodes;

it('should see all error codes', function () {
    expect(ErrorCodes::all())->toHaveCount(47);
});

it('should return message from code success', function () {
    expect(ErrorCodes::getDescription(ErrorCodes::INVALID_USER))
        ->toBe('Invalid user');
});
