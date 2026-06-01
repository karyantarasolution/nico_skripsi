<?php

namespace App\Notifications;

class WhatsAppMessage
{
    public string $content = '';

    public static function create(): self
    {
        return new self;
    }

    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
