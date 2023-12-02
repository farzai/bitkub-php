<?php

namespace Farzai\Bitkub\Constants;

class ErrorCodes
{
    public const NO_ERROR = 0;

    public const INVALID_JSON_PAYLOAD = 1;

    public const MISSING_X_BTK_APIKEY = 2;

    public const INVALID_API_KEY = 3;

    public const API_PENDING_FOR_ACTIVATION = 4;

    public const IP_NOT_ALLOWED = 5;

    public const MISSING_OR_INVALID_SIGNATURE = 6;

    public const MISSING_TIMESTAMP = 7;

    public const INVALID_TIMESTAMP = 8;

    public const INVALID_USER = 9;

    public const INVALID_PARAMETER = 10;

    public const INVALID_SYMBOL = 11;

    public const INVALID_AMOUNT = 12;

    public const INVALID_RATE = 13;

    public const IMPROPER_RATE = 14;

    public const AMOUNT_TOO_LOW = 15;

    public const FAILED_TO_GET_BALANCE = 16;

    public const WALLET_IS_EMPTY = 17;

    public const INSUFFICIENT_BALANCE = 18;

    public const FAILED_TO_INSERT_ORDER_INTO_DB = 19;

    public const FAILED_TO_DEDUCT_BALANCE = 20;

    public const INVALID_ORDER_FOR_CANCELLATION = 21;

    public const INVALID_SIDE = 22;

    public const FAILED_TO_UPDATE_ORDER_STATUS = 23;

    public const INVALID_ORDER_FOR_LOOKUP = 24;

    public const KYC_LEVEL_1_IS_REQUIRED_TO_PROCEED = 25;

    public const LIMIT_EXCEEDS = 30;

    public const PENDING_WITHDRAWAL_EXISTS = 40;

    public const INVALID_CURRENCY_FOR_WITHDRAWAL = 41;

    public const ADDRESS_IS_NOT_IN_WHITELIST = 42;

    public const FAILED_TO_DEDUCT_CRYPTO = 43;

    public const FAILED_TO_CREATE_WITHDRAWAL_RECORD = 44;

    public const NONCE_HAS_TO_BE_NUMERIC = 45;

    public const INVALID_NONCE = 46;

    public const WITHDRAWAL_LIMIT_EXCEEDS = 47;

    public const INVALID_BANK_ACCOUNT = 48;

    public const BANK_LIMIT_EXCEEDS = 49;

    public const PENDING_WITHDRAWAL_EXISTS_2 = 50;

    public const WITHDRAWAL_IS_UNDER_MAINTENANCE = 51;

    public const INVALID_PERMISSION = 52;

    public const INVALID_INTERNAL_ADDRESS = 53;

    public const ADDRESS_HAS_BEEN_DEPRECATED = 54;

    public const CANCEL_ONLY_MODE = 55;

    public const USER_HAS_BEEN_SUSPENDED_FROM_PURCHASING = 56;

    public const USER_HAS_BEEN_SUSPENDED_FROM_SELLING = 57;

    public const SERVER_ERROR = 90;

    public const DESCRIPTIONS = [
        self::NO_ERROR => 'No error',
        self::INVALID_JSON_PAYLOAD => 'Invalid JSON payload',
        self::MISSING_X_BTK_APIKEY => 'Missing X-BTK-APIKEY',
        self::INVALID_API_KEY => 'Invalid API key',
        self::API_PENDING_FOR_ACTIVATION => 'API pending for activation',
        self::IP_NOT_ALLOWED => 'IP not allowed',
        self::MISSING_OR_INVALID_SIGNATURE => 'Missing / invalid signature',
        self::MISSING_TIMESTAMP => 'Missing timestamp',
        self::INVALID_TIMESTAMP => 'Invalid timestamp',
        self::INVALID_USER => 'Invalid user',
        self::INVALID_PARAMETER => 'Invalid parameter',
        self::INVALID_SYMBOL => 'Invalid symbol',
        self::INVALID_AMOUNT => 'Invalid amount',
        self::INVALID_RATE => 'Invalid rate',
        self::IMPROPER_RATE => 'Improper rate',
        self::AMOUNT_TOO_LOW => 'Amount too low',
        self::FAILED_TO_GET_BALANCE => 'Failed to get balance',
        self::WALLET_IS_EMPTY => 'Wallet is empty',
        self::INSUFFICIENT_BALANCE => 'Insufficient balance',
        self::FAILED_TO_INSERT_ORDER_INTO_DB => 'Failed to insert order into db',
        self::FAILED_TO_DEDUCT_BALANCE => 'Failed to deduct balance',
        self::INVALID_ORDER_FOR_CANCELLATION => 'Invalid order for cancellation',
        self::INVALID_SIDE => 'Invalid side',
        self::FAILED_TO_UPDATE_ORDER_STATUS => 'Failed to update order status',
        self::INVALID_ORDER_FOR_LOOKUP => 'Invalid order for lookup',
        self::KYC_LEVEL_1_IS_REQUIRED_TO_PROCEED => 'KYC level 1 is required to proceed',
        self::LIMIT_EXCEEDS => 'Limit exceeds',
        self::PENDING_WITHDRAWAL_EXISTS => 'Pending withdrawal exists',
        self::INVALID_CURRENCY_FOR_WITHDRAWAL => 'Invalid currency for withdrawal',
        self::ADDRESS_IS_NOT_IN_WHITELIST => 'Address is not in whitelist',
        self::FAILED_TO_DEDUCT_CRYPTO => 'Failed to deduct crypto',
        self::FAILED_TO_CREATE_WITHDRAWAL_RECORD => 'Failed to create withdrawal record',
        self::NONCE_HAS_TO_BE_NUMERIC => 'Nonce has to be numeric',
        self::INVALID_NONCE => 'Invalid nonce',
        self::WITHDRAWAL_LIMIT_EXCEEDS => 'Withdrawal limit exceeds',
        self::INVALID_BANK_ACCOUNT => 'Invalid bank account',
        self::BANK_LIMIT_EXCEEDS => 'Bank limit exceeds',
        self::PENDING_WITHDRAWAL_EXISTS_2 => 'Pending withdrawal exists',
        self::WITHDRAWAL_IS_UNDER_MAINTENANCE => 'Withdrawal is under maintenance',
        self::INVALID_PERMISSION => 'Invalid permission',
        self::INVALID_INTERNAL_ADDRESS => 'Invalid internal address',
        self::ADDRESS_HAS_BEEN_DEPRECATED => 'Address has been deprecated',
        self::CANCEL_ONLY_MODE => 'Cancel only mode',
        self::USER_HAS_BEEN_SUSPENDED_FROM_PURCHASING => 'User has been suspended from purchasing',
        self::USER_HAS_BEEN_SUSPENDED_FROM_SELLING => 'User has been suspended from selling',
        self::SERVER_ERROR => 'Server error (please contact support)',
    ];

    /**
     * Get all error codes.
     */
    public static function all(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);

        return array_map(function ($constant) use ($reflection) {
            return $reflection->getConstant($constant);
        }, array_keys($reflection->getConstants()));
    }

    /**
     * Get the description of the error code.
     */
    public static function getDescription(int $code): string
    {
        return self::DESCRIPTIONS[$code] ?? "Unknown error code: {$code}";
    }
}
