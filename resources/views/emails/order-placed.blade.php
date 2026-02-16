@component('mail::message')
# New Order Awaiting Capture

An order has been placed on Woven in Agape and is awaiting payment capture.

**Order Reference:** {{ $order->reference }}
**Order Total:** {{ $order->total->formatted }}
**Status:** {{ ucfirst($order->status) }}
@if($order->placed_at)
**Placed At:** {{ $order->placed_at->format('F j, Y, g:i a') }}
@else
**Created At:** {{ $order->created_at->format('F j, Y, g:i a') }}
@endif

@if($order->user)
**Customer:** {{ $order->user->name }} ({{ $order->user->email }})
@endif

@component('mail::button', ['url' => config('app.url') . '/lunar/orders/' . $order->id])
Capture Payment in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
