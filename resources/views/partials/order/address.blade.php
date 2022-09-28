<div class="bg-white border border-gray-100 rounded-xl">
    <div class="p-6">
        <dl class="grid grid-cols-1 gap-8 text-sm sm:grid-cols-2">
            <div>
                <div class="space-y-4">
                    <div>
                        <dt class="font-medium">
                            Name
                        </dt>

                        <dd class="mt-0.5">
                            {{ $order->{$type}->first_name }} {{ $order->{$type}->last_name }}
                        </dd>
                    </div>

                    @if ($order->{$type}->company_name)
                        <div>
                            <dt class="font-medium">
                                Company
                            </dt>

                            <dd class="mt-0.5">
                                {{ $order->{$type}->company_name }}
                            </dd>
                        </div>
                    @endif

                    @if ($order->{$type}->contact_phone)
                        <div>
                            <dt class="font-medium">
                                Phone Number
                            </dt>

                            <dd class="mt-0.5">
                                {{ $order->{$type}->contact_phone }}
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="font-medium">
                            Email
                        </dt>

                        <dd class="mt-0.5">
                            {{ $order->{$type}->contact_email }}
                        </dd>
                    </div>
                </div>
            </div>

            <div>
                <dt class="font-medium">
                    Address
                </dt>

                <dd class="mt-0.5">
                    {{ $order->{$type}->line_one }}<br>
                    @if ($order->{$type}->line_two){{ $order->{$type}->line_two }}<br>@endif
                    @if ($order->{$type}->line_three){{ $order->{$type}->line_three }}<br>@endif
                    @if ($order->{$type}->city){{ $order->{$type}->city }}<br>@endif
                    @if ($order->{$type}->state){{ $order->{$type}->state }}<br>@endif
                    {{ $order->{$type}->postcode }}<br>
                    {{ $order->{$type}->country?->native }}
                </dd>
            </div>
        </dl>
    </div>
</div>