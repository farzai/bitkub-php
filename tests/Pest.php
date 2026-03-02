<?php

uses()
    ->beforeEach(function () {
        \Farzai\Bitkub\Requests\GenerateSignatureV3::resetTimestampCache();
    })
    ->in(__DIR__);
