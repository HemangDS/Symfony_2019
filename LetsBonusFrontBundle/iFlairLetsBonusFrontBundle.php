<?php

namespace iFlair\LetsBonusFrontBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class iFlairLetsBonusFrontBundle extends Bundle
{
    public function getParent()
    {
        return 'HWIOAuthBundle';
    }
}
