<?php

namespace happy\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class happySecurityBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
