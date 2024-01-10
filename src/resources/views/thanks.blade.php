<x-app-layout :hasHeader="false">
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/thanks.css') }}" />
    </x-slot>

    <div class="thanks">
        <p class="thanks__text">
            お問い合わせありがとうございました
        </p>
        <div class="thanks__link-layout">
            <a class="thanks__link" href="/">HOME</a>
        </div>
    </div>
</x-app-layout>