<?php

namespace Core\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreMessageBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSMessageBundle';
  }
}
