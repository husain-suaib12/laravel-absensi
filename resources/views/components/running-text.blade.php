@if (!empty($text))
    <div class="running-wrapper {{ $color ?? 'rt-primary' }} mb-4">
        <div class="running-text">
            {!! $text !!}
        </div>
    </div>
@endif
